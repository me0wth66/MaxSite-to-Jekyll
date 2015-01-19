<?php
/**
 * Created by me0wth66
 * Date: 1/15/2015
 * Time: 09:36 PM
 */

require("config.php");

//Check if directory exist
if ( !file_exists($file_dir)) die ( "Directory $file_dir is not found in root folder");

    // Connect to a data base
    $conn = new mysqli($db_host, $db_user, $db_password, $db_name);

    echo "<p>Проверка соединения с базой / Check connection to a database:</p>";
    // Check connection
    if (mysqli_connect_errno()) {
        //Cannot connect to a database
        echo "Соединение не удалось / Connect failed: ", mysqli_connect_error();
        exit();
    } else {
        // Connected to a database successfully
        echo "Успешно! Success! Host information: ", mysqli_get_host_info($conn);

        // Make a query
        $sql = "SELECT mso_page.page_id, mso_page.page_title, mso_page.page_slug, mso_page.page_date_publish,   mso_page.page_content, mso_category.category_slug, A.meta_value as keywords, B.meta_value as description, GROUP_CONCAT(C.meta_value SEPARATOR ', ') as tags
                FROM mso_page
                  LEFT JOIN mso_cat2obj USING (page_id)
                  LEFT JOIN mso_category USING (category_id)
                  LEFT JOIN mso_meta as A ON A.meta_id_obj = mso_page.page_id  AND A.meta_key = 'keywords'
                  LEFT JOIN mso_meta as B ON B.meta_id_obj = mso_page.page_id AND B.meta_key = 'description'
                  LEFT JOIN mso_meta as C ON C.meta_id_obj = mso_page.page_id AND C.meta_key = 'tags' GROUP BY page_id";

        //Return query
        $rows = $conn->query($sql);

        echo "<p>Список созданных файлов / List of created files:</p>" . "\n" . "<ol>";

        foreach ($rows as $row) {
            //Assign variables
            $name = $row["page_slug"];
            $date = date("Y-m-d", strtotime($row["page_date_publish"]));
            $title = $row["page_title"];
            $text = $row["page_content"];
            $category = $row["category_slug"];
            $keywords = $row["keywords"];
            $description = $row["description"];
            $tags = $row["tags"];
            
            // Set a file name
            $filename = $date . "-" . $name . ".markdown";

            // Format a file's content
            $content = "---" . "\n"
                . "layout: post" . "\n"
                . "title: " . $title . "\n"
                . "category: " . $category . "\n"
                . "tags: " . $tags . "\n"
                . "description: " . $description . "\n"
                . "keywords: " . $keywords . "\n"
                . "---" . "\n"
                . $text;

            //Create files
            if (file_put_contents("$file_dir/$filename", $content) === false) die ("Could not create files");

            //List created files
            echo "<li>" . $filename . "</li>";
        }
        echo "</ol>";

        // Close connection
        $mysqli = null;
    }

