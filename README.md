# laravel_pagination_groupby
Laravel: use query builder method 'joinSub' to solve the problem that pagination cannot include SQL 'groupBy' clause.
Laravel: use query builder method 'joinSub' to solve the problem that pagination cannot include SQL 'groupBy' clause.
I. Requirement:
1. List the books purchased.
2. Book are sorted in descending order by the last customer purchase time.
II. Analysis:
The relationship between books and purchases is one too many. Which means one book can be purchased many customers. I need all the information of the book as well as the last purchase date of this book so that I can list the books in the order of the latest purchase date.
for example:
Book A is purchased by 2 people on 1/7/2020 and 3/7/2020. The latest date 3/7/2020.
Book B is purchased by 1 people on 2/7/2020. The latest date 2/7/2020.
Book C is purchased by 3 people on 1/7/2020, 2/7/2020, 4/7/2020. The latest date is 4/7/2020.
The ideal result should be:
book_name latest_purchase_date
C 4/7/2020
A 3/7/2020
B 2/7/2020
III. Solution:
1. Tables
Table one: categories
	id(PK)
	category_name
	created_at
	updated_at
Table two: books
	id(PK)
	ISBN
	book_name
	category_id(FK)
	created_at
	updated_at
Table three: purchases
	id(PK)
	created_at
	book_id(FK)
	created_at
	updated_at

2. Controller:
class BookController extends Controller
{
	public function getBooks() {
	/* every record in the subset including 3 fields:
	book_id: indicate which book
	latest_purchase_date: the latest purchase date of the book
	purchase_number: the number of purchases of the book
	*/
	$subset = DB::table('purchases')
	->select('book_id',
			DB::raw('
				max(purchases.created_at) as latest_purchase_date,
				count(purchases.id) as purchased_number
			')
		)
	->groupBy('purchases.book_id');

	/*
	combine the purchase information with the information of the book, including two parts:
		#fields from subset
		'sub_set.*',
		#fields from table books
		'books.*'
	Therefore are 9 fields:
	book_id, latest_purchase_date, purchased_number,
	id, ISBN, book_name, category_id, created_at, updated_at
	*/
	$subsetWithBook = DB::table('books')
	->joinSub($subset, 'subset', function ($join) {
	$join->on('books.id', '=', 'subset.book_id');
	})
	->select(
		'sub_set.*',
		'books.*'
	);

	/*
	combine the purchase and book informaion with category information, which is alse composed of two parts:
		#fields from subsetWithBook
		'lastest_purchases_book.*',
		#field from table categories
		'category_name'
	There are 10 fields now:
	book_id, latest_purchase_date, purchased_number, id, ISBN, book_name, category_id, created_at, updated_at,
	category_name
	*/
	$subsetWithBookCategory = DB::table('categories')
	->joinSub($subsetWithBook, 'subset_book', function ($join) {
		$join->on('categories.id', '=', 'subset_book.category_id');
	})
	->select(
		'lastest_purchases_book.*',
		'category_name'
	)
	->orderBy('latest_purchase_date', 'desc')
	->paginate(10);
	// orderBy and paginate are chained in the end when the collection are organised completely.


	// return to the view with the collection subsetWithBookCategory
	return view('book_list')->with(compact('subsetWithBookCategory'));
	}
}

3. view
<html>
	<head></head>
<body>
	<table>
	    @foreach($subsetWithBookCategory as $item)
	    <tr>
		<td>{{$item.ISBN}}</td>
		<td>
		    <a href="book_purchases/{{item->id}}">
			{{$item.book_name}}
		    </a>
		</td>
		<td>{{$item.category_name}}</td>
		<td>{{$item.latest_purchase_date}}</td>
	    </tr>
	    @endforeach
	</table>
</body>
</html>
