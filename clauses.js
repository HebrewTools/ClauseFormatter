var state = {
  'verses': [],
  'selected': null
};

// Insert a clause break on the specified location. loc should have the keys
// verse, clause and word.
function insert_clause_break(loc) {
  console.log(loc);
  var org_clause = state.verses[loc.verse].clauses[loc.clause];
  state.verses[loc.verse].clauses.splice(loc.clause + 1, 0, {
      'indent': org_clause.indent,
      'words': org_clause.words.slice(loc.word)
  });
  state.verses[loc.verse].clauses[loc.clause].words
    = org_clause.words.slice(0, loc.word);
}

// Remove a clause break before the specified location (merge the specified
// clause with the preceeding clause). Fails if loc.clause == 0, since merging
// verses is not (yet?) supported.
function remove_clause_break(loc) {
  if (loc.clause == 0) {
    window.alert('Cannot merge verses');
    return;
  }

  state.verses[loc.verse].clauses[loc.clause - 1].words = 
      state.verses[loc.verse].clauses[loc.clause - 1].words.concat(
          state.verses[loc.verse].clauses[loc.clause].words);
  state.verses[loc.verse].clauses.splice(loc.clause, 1);
}

// Increase the indent of the clause specified by loc; give a negative
// add_indent to decrease indent.
function increase_clause_indent(loc, add_indent) {
  state.verses[loc.verse].clauses[loc.clause].indent += add_indent;
}

// Toggle the deletion state of a word
function toggle_word_deletion(loc) {
  state.verses[loc.verse].clauses[loc.clause].words[loc.word].deleted =
      !state.verses[loc.verse].clauses[loc.clause].words[loc.word].deleted;
}

// Gets text for a reference; the reference is an object with keys start_book,
// start_chapter, start_verse and their corresponding end_ alternatives.
function getText(reference, callback) {
  $.ajax('getText.php', {
        'type': 'GET',
        'data': reference,
        'dataType': 'json',
        'success': function(data, textStatus, jqXHR) {
          callback(data);
        }
      });
}

// From a clauses.php verse object (with keys verse and text), make a
// clauses.js verse object (with keys ref and an array clauses)
// Initially this is just one clause
// A clause has keys indent and words
function make_verse(verse) {
  var make_word = function(word) { return {'text': word, 'deleted': false}; }

  return {
    'ref': verse.verse,
    'clauses': [{'indent': 0, 'words': verse.text.split(' ').map(make_word)}]
  };
}

// From a clause object, make a DOM element to represent it
function make_editable_clause(clause) {
  var div = $('<div></div>');
  div.addClass('clause');
  div.css('margin-right', (clause.indent * 1.5) + 'em');
  for (var i in clause.words) {
    var word = $('<span></span>')
          .addClass('word')
          .data('wordid', i)
          .attr('onclick', 'select_word(this)')
          .text(clause.words[i].text);
    if (clause.words[i].deleted) {
      word.addClass('deleted');
    }
    div.append(word);
  }
  return div;
}

// From a verse object, make a DOM element to represent it
function make_verse_elem(verse) {
  var div = $('<div></div>');
  div.addClass('verse');
  div.append($('<span class="ref"></span>').text(verse.ref));
  for (var i in verse.clauses) {
    clause = verse.clauses[i];
    div.append(make_editable_clause(clause).data('clauseid', i));
  }
  return div;
}

// Update all DOM elements under our control
function update_document() {
  var elem = $('#text');
  elem.html('');
  for (var i in state.verses) {
    var div = make_verse_elem(state.verses[i]);
    div.data('verseid', i);
    elem.append(div);
  }

  if (state.selected) {
    var loc = state.selected;
    var wordcount = 0;
    $($($(elem.find('.verse')[loc.verse])
      .find('.clause')[loc.clause])
      .find('.word')[loc.word])
      .addClass('selected');
  }
}

// Find the new location (verse, clause, word) after changes based on only the
// verse and wordcount of the old location
function find_location(loc) {
  var clauses = state.verses[loc.verse].clauses;
  var wordcount = 0;
  for (ci in clauses) {
    var words = clauses[ci].words;
    for (wi in words) {
      if (wordcount >= loc.wordcount) {
        loc.clause = ci;
        loc.word = wi;
        return loc;
      }
      wordcount++;
    }
  }
  loc.clause = ci;
  loc.word = wi;
  loc.wordcount = wordcount - 1;
  return loc;
}

function select_word(elem) {
  var verse = parseInt($(elem).parent().parent().data('verseid'));
  var clause = parseInt($(elem).parent().data('clauseid'));
  var word = parseInt($(elem).data('wordid'));

  var get_word_count = function(v, c, w) {
    var clauses = state.verses[v].clauses;
    var wordcount = 0;
    for (ci in clauses) {
      if (ci == c) {
        return wordcount + w;
      }
      wordcount += clauses[ci].words.length;
    }
  }

  state.selected = {
    'verse': verse,
    'clause': clause,
    'word': word,
    'wordcount': get_word_count(verse, clause, word)
  };

  update_document();
}

$('#get_text_form').submit(function(){
  reference = {
    'start_book':     $('#start_book').val(),
    'start_chapter':  $('#start_chapter').val(),
    'start_verse':    $('#start_verse').val(),
    'end_book':       $('#end_book').val(),
    'end_chapter':    $('#end_chapter').val(),
    'end_verse':      $('#end_verse').val()
  };

  getText(reference, function(verses) {
    state.verses = verses.map(make_verse);
    update_document();
  });

  return false;
});

$('#make_pdf').submit(function(){
  document.forms.make_pdf.elements.verses.value = JSON.stringify(state.verses);
});

$('body').keypress(function(event){
  console.log(event);

  if (typeof event.target.form != 'undefined') {
    return true;
  }

  switch (event.keyCode) {
    case 13: // Return
      if (state.selected) {
        insert_clause_break(state.selected);
      }
      break;
    case 8: // Backspace
      if (state.selected) {
        loc = state.selected;
        if (state.verses[loc.verse].clauses[loc.clause].indent > 0) {
          increase_clause_indent(state.selected, -1);
        } else {
          remove_clause_break(state.selected);
        }
      }
      break;
    case 9: // Tab
      if (state.selected) {
        increase_clause_indent(state.selected, 1);
      }
      break;
    case 46: // Delete
      if (state.selected) {
        toggle_word_deletion(state.selected);
      }
      break;
    case 39: // Right
      if (state.selected) {
        if (state.selected.wordcount > 0)
          state.selected.wordcount--;
        state.selected = find_location(state.selected);
      }
      break;
    case 37: // Left
      if (state.selected) {
        state.selected.wordcount++;
        state.selected = find_location(state.selected);
      }
      break;
    case 38: // Up
      if (state.selected) {
        if (state.selected.verse > 0)
          state.selected.verse--;
        state.selected = find_location(state.selected);
      }
      break;
    case 40: // Down
      if (state.selected) {
        if (state.selected.verse + 1 < state.verses.length)
          state.selected.verse++;
        state.selected = find_location(state.selected);
      }
      break;
    default:
      return true;
  }

  if (state.selected) {
    state.selected = find_location(state.selected);
  }

  update_document();
  
  return false;
});

$('#saveModal').on('shown.bs.modal', function(){
  $('#saveTextarea').val(JSON.stringify(state.verses));
});

$('#restore').click(function(){
  state.verses = JSON.parse($('#saveTextarea').val());
  state.selected = null;
  update_document();
  $('#saveModal').modal('hide');
});

$('#show_help').click(function(){
  $('#help').slideToggle();
});

