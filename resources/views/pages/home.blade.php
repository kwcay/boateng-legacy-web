@extends('layouts.base')

@section('body')
	@include('layouts.header')

    <div class="title">
        Di Nkɔmɔ
        <br />
        <small>The book of Native tongues.</small>
    </div>
	<section class="form search">
		<form name="search">
			<div class="container">
				<input class="remove-btn-style" name="clear" type="button" value="&#10005;" />
				<input name="q" type="text" placeholder="start here" value="{{ Input::get('q') }}" autocomplete="off" />
				<input class="remove-btn-style" name="submit" type="submit" value="&#10163;" />
				<div class="clr"></div>
			</div>
		</form>
		<div id="results">
		    <div class="center">Use this <em>&#10548;</em> to lookup words<br />in a foreign language.</div>
		</div>
	</section>

	<script type="text/javascript">
    $(document.search).submit(function()
    {
		// Performance check
		var query	= document.search.q.value.trim();
		if (query.length < 3) {
			document.search.q.focus();
			return false;
		}
        
        // Display loading message
		$('#results').html('<div class="center">looking up '+ query +'...</div>');
        
        // Start ajax request
        $.ajax({
            url: App.root +'/definition/search/' + App.urlencode(query),
            type: 'POST',
            error: function(xhr, status, error) {
                App.log('XHR error on search form: '+ xhr.status +' ('+ error +')');
                $('#results').html('<div class="center">Seems like we ran into a snag <span class="fa fa-frown-o"></span> try again?</div>');
            },
            success: function(obj)
            {
                if (obj.results.definitions.length > 0)
                {
                    var html	=
                    '<div class="center">'+ 
                        'we found <em>'+ obj.results.definitions.length +'</em> definitions'+
                        ' for <i>'+ obj.results.query +'</i>.'+
                    '</div><ol>';
                    
                    $.each(obj.results.definitions, function(i, def) {
                        html +=
                        '<li>'+
                            '<a href="'+ def.uri +'">'+ def.word +'</a>'+
                            ' <small>('+ def.type +')</small>'+
                            ' is a word that means <i>'+ def.translation.en +'</i> in '+
                            ' <a href="'+ def.language.uri +'">'+ def.language.name +'</a>'+
                        '</li>';
                    });
                    
                    $('#results').html(html +'</ol>');
                }
                
                else {
                    $('#results').html('<div class="center">we couldn\'t find anything matching that query <span class="fa fa-frown-o"></span></div>');
                }
            }
        });
        
        return false;
    });
    
    $('input[name=clear]').click(function() {
        document.search.q.value	= '';
        $('#results').html('<div class="center">Use this <em>&#10548;</em> to lookup words<br />in a foreign language.</div>');
        document.search.q.focus();
    });
    
    $(document).ready(function() { $(document.search).submit(); });
    
	</script>

	@include('layouts.footer')
@stop
