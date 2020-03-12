<style scoped>
body {
  font-family: Menlo, Consolas, monospace;
  color: #444;
}
.item {
  cursor: pointer;
}
.bold {
  font-weight: bold;
}
ul {
  padding-left: 1em;
  line-height: 1.5em;
  list-style-type: dot;
}
</style>
<template>
    <li>
        <div
        :class="{bold: isFolder}"
        @click="toggle"
        @dblclick="makeFolder">
        {{ item.name }}
        <span v-if="isFolder">[{{ isOpen ? '-' : '+' }}]</span>
        </div>
        <ul v-show="isOpen" v-if="isFolder">
        <treedemo
            class="item"
            v-for="(child, index) in item.children"
            :key="index"
            :item="child"
            @make-folder="$emit('make-folder', $event)"
            @add-item="$emit('add-item', $event)"
        ></treedemo>
        </ul>
    </li>
</template>
<script>
export default {
    name: 'treedemo',
   props: {
    item: Object
  },
  data: function () {
    return {
      isOpen: true
    }
  },
  computed: {
    isFolder: function () {
      return this.item.children &&
        this.item.children.length
    }
  },
  methods: {
    toggle: function () {
      if (this.isFolder) {
        this.isOpen = !this.isOpen
      }
    },
    makeFolder: function () {
      if (!this.isFolder) {
      	this.$emit('make-folder', this.item)
        this.isOpen = true
      }
    }
  }
}
</script>