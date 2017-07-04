
@inject('vue', 'App\Utilities\Vue')
{{ $vue->load('word-form-modal') }}

<div id="definitionModalForm" style="display: inline-block">
    <input
        type="button"
        class="form-like"
        value="quick edit"
        @click="showModal = true">

    <word-form-modal :definition="definition" :show-modal="showModal" @toggle="toggle">
    </word-form-modal>
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
            window.console.log('Saving...');
            window.console.log(this.data);
            window.console.log(arguments);
            this.showModal = !this.showModal;
        }
    }
});
</script>
