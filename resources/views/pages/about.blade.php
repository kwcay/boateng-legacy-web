@extends('layouts.base')

@section('body')
	@include('layouts.header')

	<section>
		<h1>Di Nkɔmɔ</h1>
        <h2 class="center" style="font-weight: normal;">~ The book of Native tongues ~</h2>
		<em>Di Nkɔmɔ</em> is a multilingual dictionary focused on indigenous languages from around the world. <em>Di Nkɔmɔ</em> is <a href="aka">Akan</a> for chat, or converse. And so, we hope to encourage and increase the conversations between Native peoples all across the world.
        <br /><br />
		
		The web app was conceived and developed by Yaw Amankwa&mdash;otherwise known as <a href="http://frnk.ca" target="_blank">Frank</a>&mdash;a Montreal native originally from Ghana. It relies on great libraries and resources such as the <a href="http://fortawesome.github.io/Font-Awesome/" target="_blank">Font Awesome</a> icon set and <a href="http://scripts.sil.org/gentium" target="_blank">Gentium</a>, <i>&ldquo;a typeface for the nations&rdquo;</i> developed for SIL International. Some other resources we use are:
        <ul>
            <li>
                <a href="http://www.iso.org/iso/catalogue_detail?csnumber=39534" target="_blank">ISO 639-3 (2007)</a>: Codes for the representation of names of languages
            </li>
            <li>
                <a href="http://www.ethnologue.com/" target="_blank">Ethnologue</a>: Languages of the world.
            </li>
            <li>
                <a href="http://glottolog.org/" target="_blank">Glottolog</a>: Comprehensive reference information for the world's languages, especially the lesser known languages.
            </li>
            <li>
                <a href="http://cldr.unicode.org" target="_blank">CLDR</a>: Unicode Common Locale Data Repository
            </li>
        </ul>
        
        Check out our stats page&mdash;<a href="{{ URL::to('stats') }}"><em>Di Nkɔmɔ</em>: in numbers</a>&mdash;for more details about the app itself.
        
        <h2>Other resources</h2>
        Listed below are some of our favourite dictionaries and language resources which are available to the public:
        <ul>
            <li>
                <a href="http://maguzawa.dyndns.ws/" target="_blank">Maguzawa</a>: A Hausa-English dictionary
            </li>
            <li>
                <a href="http://twi.bb/" target="_blank">twi.bb</a>: Online Twi Dictionary
            </li>
        </ul>
	</section>

	@include('layouts.footer')
@stop
