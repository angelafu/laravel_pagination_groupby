<!DOCTYPE html>
<html>
<head>
    <title>Books sorted by latest purchase date</title>
</head>
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
<div>{{ $subsetWithBookCategory->links() }}</div>
</body>
</html>
