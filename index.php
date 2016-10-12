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
				<button type="button" class="btn btn-sm pull-right" id="show_help">help</button>
			</div>

			<div id="help">
				<div class="row">
					<div class="col-md-12">
						<p>
							Enter the start and end Bible references and hit <code>Get text</code> to load the Hebrew text (Leningrad codex). Then start editing. Click on a word to select it. Then use any of the following keys:
						</p>
						<ul>
							<li><code>Return</code> (Enter), to insert a clause break.</li>
							<li><code>Tab</code>, to indent the selected clause.</li>
							<li><code>Backspace</code>, to unindent the selected clause. When the clause is not indented at all, the clause break is removed (the clause is appended to the previous clause).</li>
							<li><code>Delete</code>, to delete a word. It will still be visible, so you can undelete it (with <code>Delete</code> as well), but won't appear in the PDF output.</li>
							<li><code>p</code> and <code>s</code>, to (un)mark words as predicates or subjects, respectively. They will be coloured blue and red, also in the PDF output.</li>
							<li><code>d</code> to (un)set a diacritical sign for the clause, to rewind numbering to the last clause with the same indentation.</li>
							<li>Arrow keys to move around.</li>
						</ul>
						<p>When you're done, use <code>PDF</code> to create a PDF document. With <code>TeX</code> you get the (Xe)LaTeX source for that document. It depends among other things on <code>clauses.sty</code>. With <code>Zip</code>, you can get a ZIP file with this file, the generated (Xe)LaTeX source and the compiled PDF.</p>
						<p>You can save your work using <code>Save / Restore</code>. Copy the text in the field to somewhere secure. When you want to continue your work, use the same button, input the saved text and hit <code>Restore</code>.</p>
						<p>During editing, only verse numbers (1, 2, 3, ...) are shown. The PDF will have subnumbering (1a, 1b, ...).</p>
						<p>This is open source software, licensed under GPL v3.0. Written by <a href="https://camilstaps.nl">Camil Staps</a>. See <a href="https://github.com/HebrewTools/ClauseFormatter">GitHub</a>.</p>
					</div>
				</div>
				<hr/>
			</div>

			<div id="controls">
				<div class="row">
					<form class="form-inline" action="#" id="get_text_form">
						<div class="col-sm-8 col-md-7">
							<div class="form-group">
								<select id="book" class="form-control input-book">
									<?php
										foreach ($all_books as $id => $book)
											echo "<option value='$id'>$book</option>";
									?>
								</select>
							</div>

							<div class="form-group">
								<div class="input-group">
									<input type="text" class="form-control input-chapter" id="start_chapter" placeholder="Chapter"/>
									<span class="input-group-btn" style="width:0px;"></span>
									<input type="text" class="form-control input-verse" id="start_verse" placeholder="Verse"/>
								</div>
							</div>

							<label>to</label>
							<div class="form-group">
								<div class="input-group">
									<input type="text" class="form-control input-chapter" id="end_chapter" placeholder="Chapter"/>
									<span class="input-group-btn" style="width:0px;"></span>
									<input type="text" class="form-control input-verse" id="end_verse" placeholder="Verse"/>
								</div>
							</div>

							<div class="form-group">
								<button class="btn btn-sm btn-primary" id="get_text">Fetch</button>
							</div>
						</div>
					</form>

					<div class="col-sm-4 col-md-5" id="output-options">
						<form action="makePdf.php" name="make_pdf" method="post" target="_blank" id="make_pdf" class="form-inline">
							<input type="hidden" name="verses"/>
							<div class="form-group">
								<button class="btn btn-sm btn-success">PDF</button>
							</div>
						</form>
						<form action="makeTeX.php" name="make_tex" method="post" target="_blank" id="make_tex" class="form-inline">
							<input type="hidden" name="verses"/>
							<div class="form-group">
								<button class="btn btn-sm btn-warning">TeX</button>
							</div>
						</form>
						<form action="makeZip.php" name="make_zip" method="post" target="_blank" id="make_zip" class="form-inline">
							<input type="hidden" name="verses"/>
							<div class="form-group">
								<button class="btn btn-sm btn-warning">Zip</button>
							</div>
						</form>
						<form action="#" class="form-inline">
							<div class="form-group">
								<a class="btn btn-sm btn-info" data-toggle="modal" href="#saveModal">Save / Restore</a>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div id="text" dir="rtl"></div>

			<div class="modal fade" id="saveModal" role="dialog" tabindex="-1">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">×</button>
							<h3>Save and restore</h3>
						</div>
						<div class="modal-body">
							<p>Save this text somewhere, or input an old text to restore:</p>
							<textarea id="saveTextarea" class="form-control" rows="6"></textarea>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn" data-dismiss="modal">Close</button>
							<button type="button" class="btn btn-primary" id="restore">Restore</button>
						</div>
					</div>
				</div>
			</div>

		</div>
	</body>
</html>
