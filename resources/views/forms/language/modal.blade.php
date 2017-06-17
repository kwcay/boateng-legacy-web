
<div id="languageModalForm">
    <a href="javascript:void" @click="showModal = true">
        edit <i class="fa fa-pencil fa-fw"></i>
    </a>

    <!-- use the modal component, pass in the prop -->
    <modal v-if="showModal" @close="showModal = false">
        <!--
          you can use custom content here to overwrite
          default content
        -->
        <h3 slot="header">TODO: edit language</h3>
    </modal>
</div>

{{-- TODO: Move this template inside some build script --}}
<script type="text/x-template" id="language-modal-form">
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">

                    <div class="modal-header">
                        <slot name="header">
                            default header
                        </slot>
                    </div>

                    <div class="modal-body">
                        <slot name="body">
                            default body
                        </slot>
                    </div>

                    <div class="modal-footer">
                        <slot name="footer">
                            default footer
                            <button class="modal-default-button" @click="$emit('close')">
                                OK
                            </button>
                        </slot>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</script>

<script type="text/javascript">
    // register modal component
    Vue.component('modal', {
        template: '#language-modal-form'
    })

    // start app
    new Vue({
        el: '#languageModalForm',
        data: {
            showModal: false
        }
    })
</script>
