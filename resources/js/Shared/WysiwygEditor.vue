<template>
  <div class="wysiwyg">
    <label v-if="label" class="form-label">{{ label }}:</label>
    <div v-if="editor" class="wysiwyg-toolbar flex flex-wrap gap-1 p-2 border border-b-0 rounded-t bg-gray-50 mt-1">
      <button type="button" class="tb-btn" :class="{ active: editor.isActive('bold') }" @click="editor.chain().focus().toggleBold().run()"><b>B</b></button>
      <button type="button" class="tb-btn" :class="{ active: editor.isActive('italic') }" @click="editor.chain().focus().toggleItalic().run()"><i>I</i></button>
      <button type="button" class="tb-btn" :class="{ active: editor.isActive('strike') }" @click="editor.chain().focus().toggleStrike().run()"><s>S</s></button>
      <span class="tb-sep" />
      <button type="button" class="tb-btn" :class="{ active: editor.isActive('heading', { level: 2 }) }" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()">H2</button>
      <button type="button" class="tb-btn" :class="{ active: editor.isActive('heading', { level: 3 }) }" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()">H3</button>
      <span class="tb-sep" />
      <button type="button" class="tb-btn" :class="{ active: editor.isActive('bulletList') }" @click="editor.chain().focus().toggleBulletList().run()">• Lista</button>
      <button type="button" class="tb-btn" :class="{ active: editor.isActive('orderedList') }" @click="editor.chain().focus().toggleOrderedList().run()">1. Lista</button>
      <span class="tb-sep" />
      <button type="button" class="tb-btn" :class="{ active: editor.isActive('link') }" @click="setLink">Link</button>
      <button type="button" class="tb-btn" :disabled="!editor.isActive('link')" @click="editor.chain().focus().unsetLink().run()">Usuń link</button>
      <span class="tb-sep" />
      <button type="button" class="tb-btn" :disabled="!editor.can().undo()" @click="editor.chain().focus().undo().run()">↶</button>
      <button type="button" class="tb-btn" :disabled="!editor.can().redo()" @click="editor.chain().focus().redo().run()">↷</button>
    </div>
    <editor-content :editor="editor" class="wysiwyg-content border rounded-b p-3 bg-white min-h-[200px]" :class="{ error: error }" />
    <div v-if="error" class="form-error">{{ error }}</div>
  </div>
</template>

<script>
import { Editor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Link from '@tiptap/extension-link'

export default {
  components: { EditorContent },
  props: {
    modelValue: { type: String, default: '' },
    label: { type: String, default: '' },
    error: { type: String, default: '' },
  },
  emits: ['update:modelValue'],
  data() {
    return { editor: null }
  },
  watch: {
    modelValue(value) {
      if (!this.editor) return
      if (this.editor.getHTML() === value) return
      this.editor.commands.setContent(value || '', false)
    },
  },
  mounted() {
    this.editor = new Editor({
      extensions: [
        StarterKit,
        Link.configure({ openOnClick: false, HTMLAttributes: { rel: 'noopener noreferrer', target: '_blank' } }),
      ],
      content: this.modelValue || '',
      onUpdate: ({ editor }) => {
        this.$emit('update:modelValue', editor.getHTML())
      },
    })
  },
  beforeUnmount() {
    if (this.editor) this.editor.destroy()
  },
  methods: {
    setLink() {
      const previous = this.editor.getAttributes('link').href
      const url = window.prompt('URL linku:', previous || 'https://')
      if (url === null) return
      if (url === '') {
        this.editor.chain().focus().extendMarkRange('link').unsetLink().run()
        return
      }
      this.editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
    },
    insertAtCursor(text) {
      if (!this.editor) return
      this.editor.chain().focus().insertContent(text).run()
    },
  },
}
</script>

<style>
.wysiwyg .tb-btn {
  padding: 4px 8px;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  background: #fff;
  font-size: 13px;
  line-height: 1;
  cursor: pointer;
}
.wysiwyg .tb-btn:hover { background: #f3f4f6; }
.wysiwyg .tb-btn.active { background: #4338ca; color: #fff; border-color: #4338ca; }
.wysiwyg .tb-btn:disabled { opacity: .4; cursor: not-allowed; }
.wysiwyg .tb-sep { width: 1px; background: #d1d5db; margin: 0 4px; }
.wysiwyg-content .ProseMirror { outline: none; min-height: 180px; }
.wysiwyg-content .ProseMirror p { margin: 0 0 .5em; }
.wysiwyg-content .ProseMirror h2 { font-size: 1.25rem; font-weight: 700; margin: .5em 0; }
.wysiwyg-content .ProseMirror h3 { font-size: 1.1rem; font-weight: 600; margin: .5em 0; }
.wysiwyg-content .ProseMirror ul { list-style: disc; padding-left: 1.5em; }
.wysiwyg-content .ProseMirror ol { list-style: decimal; padding-left: 1.5em; }
.wysiwyg-content .ProseMirror a { color: #4338ca; text-decoration: underline; }
.wysiwyg-content.error { border-color: #dc2626; }
</style>
