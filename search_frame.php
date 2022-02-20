
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
    <meta http-equiv="Content-type" content="application/xhtml+xml;charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <link rel="stylesheet" type="text/css" href="static/styles.css"/>
    </head>
    <body>
        <?php
            function print_next_pages($page, $button_val, $q) 
            {
                echo "<form id=\"page\" action=\"search.php\" target=\"_top\" method=\"post\">";
                echo "<input type=\"hidden\" name=\"p\" value=\"" . $page . "\" />";
                echo "<input type=\"hidden\" name=\"q\" value=\"$q\" />";
                echo "<button type=\"submit\">$button_val</button>";
                echo "</form>"; 
            }
            
            session_start();

            require_once "google.php";
            require_once "tools.php";

            $query = $_SESSION["q"];
            $page = (int) htmlspecialchars($_SESSION["p"]);
            $type = (int) $_SESSION["type"];

            $start_time = microtime(true);
            $results = get_google_results($query, $page, $type);
            $end_time = number_format(microtime(true) - $start_time, 2, '.', '');

            echo "<p id=\"time\">Fetched the results in $end_time seconds</p>";
            
            if ($type == 0) // text search
            {
                check_for_special_search($query);
                
                foreach($results as $result)
                {
                    $title = $result["title"];
                    $url = $result["url"];
                    $base_url = $result["base_url"];
                    $description = $result["description"];

                    echo "<div class=\"result-container\">";
                    echo "<a href=\"$url\" target=\"_blank\">";
                    echo "$base_url";
                    echo "<h2>$title</h2>";
                    echo "</a>";
                    echo "<span>$description</span>";
                    echo "</div>";
                }
                
                echo "<div class=\"page-container\">";

                if ($page != 0) 
                {
                    print_next_pages(0, "&lt;&lt;", $query);
                    print_next_pages($page - 10, "&lt;", $query);
                }
                
                for ($i=$page / 10; $page / 10 + 10 > $i; $i++)
                {
                    $page_input = $i * 10;
                    $page_button = $i + 1;
                    
                    print_next_pages($page_input, $page_button, $query);
                }

                print_next_pages($page + 10, "&gt;", $query);

                echo "</div>";
            }
            else if ($type == 1) // image search
            {

                echo "<div class=\"image-result-container\">";

                foreach($results as $result)
                {
                    $src = $result["base64"];
                    $alt = $result["alt"];
    
                    echo "<a title=\"$alt\" href=\"data:image/jpeg;base64,$src\" target=\"_blank\">";
                    echo "<img src=\"data:image/jpeg;base64,$src\">";
                    echo "</a>";
                }

                echo "</div>";
            }

            better_session_destroy();

        ?>
    </body>
</html>