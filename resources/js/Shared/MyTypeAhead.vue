<template>
    <div class="relative">
        <label v-if="true" class="block w-full text-sm font-medium text-gray-700 ml-2" :for="id">Ein Label</label>

        <input
            ref="inputRef"
            :id="id"
            class="block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 rounded-md"
            type="text"
            :placeholder="placeholder"
            :value="modelValue"
            @input="onInput"
            @focus="onFocus"
            @blur="onBlur"
            @keydown.down.prevent="onArrowDown"
            @keydown.up.prevent="onArrowUp"
            @keydown.enter.tab.prevent="selectCurrentSelection"
            autocomplete="off"
        />
        <div v-if="true" class="absolute w-full border-none overflow-y-auto border-b-2 border-b-blue-500 z-10 max-h-24" id="list">
            <div
                class="simple-typeahead-list-item"
                :class="{ 'item-active': currentSelectionIndex == index }"
                v-for="(item, index) in filteredItems"
                :key="index"
                @mousedown.prevent
                @click="selectItem(item)"
                @mouseenter="currentSelectionIndex = index"
            >
                <span class="simple-typeahead-list-item-text" :data-text="itemProjection(item)" v-html="boldMatchText(itemProjection(item))"></span>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent } from 'vue';

export default /*#__PURE__*/ defineComponent({
    name: 'Typeahead',
    emits: ['onInput', 'onFocus', 'onBlur', 'selectItem'],
    props: {
        id: {
            type: String,
        },
        placeholder: {
            type: String,
            default: '',
        },
        items: {
            type: Array,
            required: true,
        },
        defaultItem: {
            default: null,
        },
        itemProjection: {
            type: Function,
            default(item) {
                return item;
            },
        },
        minInputLength: {
            type: Number,
            default: 2,
            validator: (prop) => {
                return prop >= 0;
            },
        },
        modelValue: String,
    },
    mounted() {
        if (this.defaultItem !== undefined && this.defaultItem !== null) {
            this.selectItem(this.defaultItem);
        }
    },
    data() {
        return {
            inputId: this.id || `simple_typeahead_${(Math.random() * 1000).toFixed()}`,
            input: '',
            isInputFocused: false,
            currentSelectionIndex: 0,
        };
    },
    methods: {
        onInput() {
            console.log('onInput');
            if (this.isListVisible && this.currentSelectionIndex >= this.filteredItems.length) {
                this.currentSelectionIndex = (this.filteredItems.length || 1) - 1;
            }
            this.$emit('onInput', { input: this.input, items: this.filteredItems });
        },
        onFocus() {
            this.isInputFocused = true;
            this.$emit('onFocus', { input: this.input, items: this.filteredItems });
        },
        onBlur() {
            this.isInputFocused = false;
            this.$emit('onBlur', { input: this.input, items: this.filteredItems });
        },
        onArrowDown($event) {
            if (this.isListVisible && this.currentSelectionIndex < this.filteredItems.length - 1) {
                this.currentSelectionIndex++;
            }
            this.scrollSelectionIntoView();
        },
        onArrowUp($event) {
            if (this.isListVisible && this.currentSelectionIndex > 0) {
                this.currentSelectionIndex--;
            }
            this.scrollSelectionIntoView();
        },
        scrollSelectionIntoView() {
            setTimeout(() => {
                const list_node = document.querySelector("#list");
                const active_node = document.querySelector("#list .item-active");

                if (!(active_node.offsetTop >= list_node.scrollTop && active_node.offsetTop + active_node.offsetHeight < list_node.scrollTop + list_node.offsetHeight)) {
                    let scroll_to = 0;
                    if (active_node.offsetTop > list_node.scrollTop) {
                        scroll_to = active_node.offsetTop + active_node.offsetHeight - list_node.offsetHeight;
                    } else if (active_node.offsetTop < list_node.scrollTop) {
                        scroll_to = active_node.offsetTop;
                    }

                    list_node.scrollTo(0, scroll_to);
                }
            });
        },
        selectCurrentSelection() {
            if (this.currentSelection) {
                this.selectItem(this.currentSelection);
            }
        },
        selectItem(item) {
            this.input = this.itemProjection(item);
            this.currentSelectionIndex = 0;
            this.$refs.inputRef.blur();
            this.$emit('selectItem', item);
        },
        escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        },
        boldMatchText(text) {
            const regexp = new RegExp(`(${this.escapeRegExp(this.input)})`, 'ig');
            return text.replace(regexp, '<strong>$1</strong>');
        },
        clearInput() {
            this.input = '';
        },
        getInput() {
            return this.$refs.inputRef;
        },
        focusInput() {
            this.$refs.inputRef.focus();
            this.onFocus();
        },
        blurInput() {
            this.$refs.inputRef.blur();
            this.onBlur();
        },
    },
    computed: {
        wrapperId() {
            return `${this.inputId}_wrapper`;
        },
        filteredItems() {
            const regexp = new RegExp(this.escapeRegExp(this.input), 'i');

            return this.items.filter((item) => this.itemProjection(item).match(regexp));
        },
        isListVisible() {
            return true;
            return this.isInputFocused && this.input.length >= this.minInputLength && this.filteredItems.length;
        },
        currentSelection() {
            return this.isListVisible && this.currentSelectionIndex < this.filteredItems.length ? this.filteredItems[this.currentSelectionIndex] : undefined;
        },
    },
});
</script>

<style scoped>
.simple-typeahead {
    position: relative;
    width: 100%;
}
.simple-typeahead > input {
    margin-bottom: 0;
}
.simple-typeahead .simple-typeahead-list {
    position: absolute;
    width: 100%;
    border: none;
    max-height: 400px;
    overflow-y: auto;
    border-bottom: 0.1rem solid #d1d1d1;
    z-index: 9;
}
.simple-typeahead .simple-typeahead-list .simple-typeahead-list-header {
    background-color: #fafafa;
    padding: 0.6rem 1rem;
    border-bottom: 0.1rem solid #d1d1d1;
    border-left: 0.1rem solid #d1d1d1;
    border-right: 0.1rem solid #d1d1d1;
}
.simple-typeahead .simple-typeahead-list .simple-typeahead-list-footer {
    background-color: #fafafa;
    padding: 0.6rem 1rem;
    border-left: 0.1rem solid #d1d1d1;
    border-right: 0.1rem solid #d1d1d1;
}
.simple-typeahead .simple-typeahead-list .simple-typeahead-list-item {
    cursor: pointer;
    background-color: #fafafa;
    padding: 0.6rem 1rem;
    border-bottom: 0.1rem solid #d1d1d1;
    border-left: 0.1rem solid #d1d1d1;
    border-right: 0.1rem solid #d1d1d1;
}

.simple-typeahead .simple-typeahead-list .simple-typeahead-list-item:last-child {
    border-bottom: none;
}

.simple-typeahead .simple-typeahead-list .simple-typeahead-list-item.simple-typeahead-list-item-active {
    background-color: #e1e1e1;
}
</style>
