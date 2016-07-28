
@if (env('APP_ENV') == 'production')
<script type="text/javascript">
  var keenClient = new Keen({
    projectId: "{{ env('KEEN_PROJECT_ID') }}",
    writeKey: "{{ env('KEEN_WRITE_KEY') }}",
    protocol: "auto",
    host: "api.keen.io/3.0",
    requestType: "jsonp"
  });
</script>
@endif
