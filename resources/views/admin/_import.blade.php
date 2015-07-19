

		Through a YAML file
		<form class="form edit" method="post" action="/import">

		    <input type="file" name="data" /><br />

		    <select name="resource">
		        <option value="def" selected>Definitions</option>
		        <option value="lang">Languages</option>
		    </select>

		    <input type="submit" value="Upload file" />

		    <input type="hidden" name="format" value="yaml" />
		    <input type="hidden" name="medium" value="file" />
			{!! Form::token() !!}
		</form>

		<form class="form edit" method="post" action="/import">
		    <textarea name="data" placeholder="Or paste plain text here (still YAML)"></textarea><br />

		    <select name="resource">
		        <option value="definition">Definitions</option>
		        <option value="language" selected>Languages</option>
		    </select>

		    <input type="submit" value="Send plain text" />

		    <input type="hidden" name="format" value="yaml" />
		    <input type="hidden" name="medium" value="plain" />
			{!! Form::token() !!}
		</form>