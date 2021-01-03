<!DOCTYPE html>

<html>
    <head>
        <title>Optimizer</title>
        <meta charset="UTF-8">
        <meta name="author" content="Klaus Thorres">
        <style>
            body {
                background-color: whiteSmoke;
            }

            table {
                border-collapse: collapse;
                border-top: double;
                border-bottom: double;
                margin-bottom: 10px;
            }

            th {
                border-bottom: solid 1px;
                padding-left: 20px;
            }

            td {
                text-align: right;
            }

            .text {
                text-align: left;
            }

            ul {
                margin-top: 5px;
            }
        </style>
    </head>
    <body onload="display('check_data')">
        <h1>Optimizer</h1>
        <noscript>Please activate Javascript.</noscript>
        <div id="check_data">
        </div>
        <div id="output_result">
        </div>
        <script>
            function display(part) {
                var xmlhttp=new XMLHttpRequest();
                xmlhttp.onreadystatechange=function() {
                    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                        var response=xmlhttp.responseText;
                        document.getElementById(part).innerHTML=response;
                    }
                }
                var file = "index.php?display=" + part;
                xmlhttp.open("GET", file, true);
                xmlhttp.send();
            }
        </script>
    </body>
</html>
