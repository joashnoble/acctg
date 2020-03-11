<style>
    .select2-container--bootstrap4 .select2-selection {
        font-size: .72rem!important;
        height:calc(1.5em + 0.5rem + 5px)!important;
    }

    .select2-container--bootstrap4 .select2-selection--single {
        line-height: 1.3;
    }

    .select2-search--dropdown .select2-search__field{
        font-size: .72rem!important;
    }
    
    .select2-results__option {
        font-size: .72rem!important;
    }
    
    .select2-container--open {
        z-index:2500;
    }
</style>

<template>
    <b-form-select>
        <slot></slot>
    </b-form-select>
</template>
<script>
    export default {
        props: ['options', 'value', 'placeholder', 'allowClear','reference'],
        mounted(){
            var vm = this
            $('.modal-content').removeAttr('tabindex')

            $(this.$el)
            .select2({ 
                // data: this.options,
                theme: 'bootstrap4',
                placeholder: this.placeholder,
                allowClear: this.allowClear
            })
            .val(this.value)
            .trigger('change')
            .on('select2:select', function () {
                var option_data = $(this).select2('data')
                vm.$emit('input', this.value, option_data, vm.reference)
            })
        },
        watch: {
            value: function (value) {
                $(this.$el)
                    .val(value)
                    .trigger('change')
            },
            options: function (options) {
                $(this.$el).empty().select2({
                        theme: 'bootstrap4',
                        // data: options,
                        placeholder: this.placeholder,
                        allowClear: this.allowClear
                    })
                    .val(this.value)
                    .trigger('change')
            }
        },
        destroyed: function () {
            $(this.$el).off().select2('destroy')
        }
    }
</script>