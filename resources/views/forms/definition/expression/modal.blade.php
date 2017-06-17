
@inject('vue', 'App\Utilities\Vue')
{{ $vue->load('expression-modal-form') }}

<div id="definitionModalForm" style="display: inline-block">
    <input
        type="button"
        class="form-like"
        value="quick edit"
        @click="showModal = true">

    <expression-modal-form :definition="definition" :show-modal="showModal" @toggle="toggle">
    </expression-modal-form>
</div>

<script type="text/javascript">
new Vue({
    el: '#definitionModalForm',
    data: {
        showModal: false,
        definition: {!! json_encode((array) $definition) !!}
    },
    methods: {
        toggle: function () {
            this.showModal = !this.showModal;
        }
    }
});
</script>
