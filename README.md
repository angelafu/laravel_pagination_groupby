# laravel_pagination_groupby
Laravel: use query builder method 'joinSub' to solve the problem that pagination cannot include SQL 'groupBy' clause.

I. Requirement:
1. List the books purchased.
2. Book are sorted in descending order by the last customer purchase time.

II. Analysis:
The relationship between books and purchases is one too many. Which means one book can be purchased many customers. I need all the information of the book as well as the last purchase date of this book so that I can list the books in the order of the latest purchase date.
<br>for example:
<br>Book A is purchased by 2 people on 1/7/2020 and 3/7/2020. The latest date 3/7/2020.
<br>Book B is purchased by 1 people on 2/7/2020. The latest date 2/7/2020.
<br>Book C is purchased by 3 people on 1/7/2020, 2/7/2020, 4/7/2020. The latest date is 4/7/2020.
<br>The ideal result should be:
<br>book_name latest_purchase_date
<br>C 4/7/2020
<br>A 3/7/2020
<br>B 2/7/2020

III. Solution:
1. Tables<br>
Table 1: categories<br>
id(PK), category_name, created_at, updated_at<br>
Table 2: books<br>
id(PK), ISBN, book_name, created_at ,updated_at, category_id(FK)<br>
Table 3: purchases<br>
id(PK), created_at, updated_at, book_id(FK)

2. Controller:BookController.php

3. View: book_list.blade.php
