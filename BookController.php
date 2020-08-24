class BookController extends Controller
{
	public function getBooks() {
	/************************
	every record in the subset including 3 fields:
	book_id: indicate which book
	latest_purchase_date: the latest purchase date of the book
	purchase_number: the number of purchases of the book
	***********************/
	$subset = DB::table('purchases')
	->select('book_id',
			DB::raw('
				max(purchases.created_at) as latest_purchase_date,
				count(purchases.id) as purchased_number
			')
		)
	->groupBy('purchases.book_id');

	/***********************
	combine the purchase information with the information of the book, including two parts:
		#fields from subset
		'sub_set.*',
		#fields from table books
		'books.*'
	Therefore are 9 fields:
	book_id, latest_purchase_date, purchased_number,
	id, ISBN, book_name, category_id, created_at, updated_at
	***********************/

	$subsetWithBook = DB::table('books')
	->joinSub($subset, 'sub_set', function ($join) {
	$join->on('books.id', '=', 'sub_set.book_id');
	})
	->select(
		'sub_set.*',
		'books.*'
	);

	/************************
	combine the purchase and book informaion with category information, which is alse composed of two parts:
		#fields from subsetWithBook
		'latest_purchases_book.*',
		#field from table categories
		'category_name'
	There are 10 fields now:
	book_id, latest_purchase_date, purchased_number, id, ISBN, book_name, category_id, created_at, updated_at,
	category_name
	************************/

	$subsetWithBookCategory = DB::table('categories')
	->joinSub($subsetWithBook, 'subset_book', function ($join) {
		$join->on('categories.id', '=', 'subset_book.category_id');
	})
	->select(
		'subset_book.*',
		'category_name'
	)
	->orderBy('latest_purchase_date', 'desc')
	->paginate(10);
	// orderBy and paginate are chained in the end when the collection are organised completely.


	// return to the view with the collection subsetWithBookCategory
	return view('book_list')->with(compact('subsetWithBookCategory'));
	}
}