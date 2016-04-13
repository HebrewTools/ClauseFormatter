<!DOCTYPE html>
<?php
  require_once('clauses.php');
  $all_books = get_all_books();
?>
<html lang="en">
  <head>
    <title>Hebrew Clause Formatter</title>
    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <link rel="stylesheet" href="controls.css">
    <link rel="stylesheet" href="clauses.css">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

    <script src="clauses.js" defer="defer"></script>
  </head>
  <body role="application">
    <div class="container" role="main">

      <div class="page-header">
        <h1>Hebrew Clause Formatter</h1>
      </div>

      <div id="controls">
        <div class="row">
          <form class="form-inline" action="#" id="get_text_form">
            <div class="col-md-5">
              <label>From:</label>
              <div class="form-group">
                <div class="input-group">
                  <select id="start_book" class="form-control input-book">
                    <?php
                      foreach ($all_books as $id => $book) {
                        echo "<option value='$id'>$book</option>";
                      }
                    ?>
                  </select>
                  <span class="input-group-btn" style="width:0px;"></span>
                  <input type="text" class="form-control input-chapter" id="start_chapter" placeholder="Chapter"/>
                  <span class="input-group-btn" style="width:0px;"></span>
                  <input type="text" class="form-control input-verse" id="start_verse" placeholder="Verse"/>
                </div>
              </div>
            </div>

            <div class="col-md-5">
              <label>To:</label>
              <div class="form-group">
                <div class="input-group">
                  <select id="end_book" class="form-control input-book">
                    <?php
                      foreach ($all_books as $id => $book) {
                        echo "<option value='$id'>$book</option>";
                      }
                    ?>
                  </select>
                  <span class="input-group-btn" style="width:0px;"></span>
                  <input type="text" class="form-control input-chapter" id="end_chapter" placeholder="Chapter"/>
                  <span class="input-group-btn" style="width:0px;"></span>
                  <input type="text" class="form-control input-verse" id="end_verse" placeholder="Verse"/>
                </div>
              </div>
            </div>
  
            <div class="col-md-1">
              <div class="form-group">
                <button class="btn btn-sm btn-primary" id="get_text">Get text</button>
              </div>
            </div>
          </form>
          <form action="makePdf.php" name="make_pdf" method="post" target="_blank" id="make_pdf">
            <input type="hidden" name="verses"/>
            <div class="col-md-1">
              <div class="form-group">
                <button class="btn btn-sm btn-success">Make PDF</button>
              </div>
            </div>
          </form>
        </div>
      </div>
  
      <div id="text" dir="rtl"></div>

    </div>
  </body>
</html>
