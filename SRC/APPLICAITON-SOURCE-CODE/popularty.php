<!DOCTYPE html>
<html>
<head>
    <style>
        p {
            font-family: "Comic Sans MS", cursive, sans-serif;
            text-align: center;
            font-size: 250%;
        }
        h1 {
            font-family: "Comic Sans MS", cursive, sans-serif;
            text-align: center;
            font-size: 150%;
        }
        table {
            border-spacing: 0.5rem;
            margin-left: 30%;
            margin-right: 15%;
        }

        tr {
            font-family: "Comic Sans MS", cursive, sans-serif;
            font-size: 100%;
            padding: 0.5rem;
        }

        tr:nth-child(6n+0) {
            background: hsl(150, 50%, 50%);
        }

        tr:nth-child(6n+1) {
            background: hsl(160, 60%, 50%);
        }

        tr:nth-child(6n+2) {
            background: hsl(170, 70%, 50%);
        }

        tr:nth-child(6n+3) {
            background: hsl(180, 80%, 50%);
        }

        tr:nth-child(6n+4) {
            background: hsl(190, 90%, 50%);
        }

        tr:nth-child(6n+5) {
            background: hsl(200, 99%, 50%);
        }
    </style>

</head>
<body style="background-image:url(background.jpg)">
<p>popularty is you thing?</p>
<h1>here are the most popular restrurant and food category in every city:</h1>

<?php
require_once("connectToDB.php");
$conn = connect();
//select the most popular restaurant in the city by number of checkins,
// and the most popular category in the city, by the amount of restaurants in the category.

$sql = "
SELECT c.name AS city_name , r.name AS rest_name,r.id AS r_id , max(r.checkinsCount) AS chekins, best_cat.cat_name
FROM Restaurant r, City c, Category cat,
(
	SELECT x.city_name, y.cat_name
	FROM
		(SELECT city_categories.city_name, max(city_categories.cnt) AS m
		FROM
			(
			SELECT distinct City.name AS city_name, c.name AS cat_name,count(c.name) AS cnt
			FROM Restaurant r, Category c, City
			WHERE r.category_id = c.id and City.id= r.city_id
			GROUP BY  City.name,c.name
			) AS city_categories

	GROUP BY city_categories.city_name) AS x ,
 	(
			SELECT distinct City.name AS city_name, c.name AS cat_name,count(c.name) AS cnt
			FROM Restaurant r, Category c, City
			WHERE r.category_id = c.id and City.id= r.city_id
			GROUP BY  City.name,c.name
	) AS y
	WHERE x.city_name= y.city_name and x.m= y.cnt
) AS best_cat 
WHERE r.city_id=c.id and r.category_id= cat.id and best_cat.city_name= c.name
GROUP BY c.name";
$result = $conn->query($sql);
?>

<table>
    <tr>
        <td><b>City</b></td>
        <td><b>The most popular restaurant</b></td>
        <td><b>The most popular category</b></td>
    </tr>
	
    <?php for ($i = 0; $i < $result->num_rows; $i++) { 
	// for each city, we build a row in the table to represent the resaults.
	?>
        <tr>

            <?php $row = $result->fetch_assoc(); ?>
            <td><?php echo $row["city_name"]; ?> </td>
            <td>
                <a href="rest.php?id=<?php echo $row["r_id"]; ?>"><?php echo $row["rest_name"]; ?> </a>
            </td>
            <td> <?php echo $row["cat_name"]; ?> </td>
        </tr>
        <?php
    } ?>

	
<?php	
//button to move the previous page 
?>
</table>
<h2 align="center">
    <h3 align="center">
        <input style="text-align: center
				width: 170px;
				padding: 30px;
				margin: auto;
				cursor: pointer;
				position: center;
				box-shadow: 6px 6px 5px; #999;
				-webkit-box-shadow: 6px 6px 5px #999;
				-moz-box-shadow: 6px 6px 5px #999;
				font-weight: bold;
				background: #8fbc8f;
				color: #000;
				border-radius:
				10px; border: 1px solid #999;
				font-size: 150%;"
               type="button"
               value="back"
               align="center"
               onclick="window.location.href='welcome.php'"/>
        <input style="text-align: center
				width: 170px;
				padding: 30px;
				margin: auto;
				cursor: pointer;
				position: center;
				box-shadow: 6px 6px 5px; #999;
				-webkit-box-shadow: 6px 6px 5px #999;
				-moz-box-shadow: 6px 6px 5px #999;
				font-weight: bold;
				background: #8fbc8f;
				color: #000;
				border-radius:
				10px; border: 1px solid #999;
				font-size: 150%;"
               type="button"
               value="new search"
               align="center"
               onclick="window.location.href='welcome.php'"/>
    </h3>
</h2>

<?php
//close the connection to the database
 $conn->close(); ?>
</body>
</html>
