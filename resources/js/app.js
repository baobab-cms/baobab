import Alpine from 'alpinejs';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Underline from '@tiptap/extension-underline';
import TextAlign from '@tiptap/extension-text-align';
import Highlight from '@tiptap/extension-highlight';
import FormEmbed from './tiptap/form-embed-node.js';

window.Alpine = Alpine;
window.Cropper = Cropper;
window.TiptapEditor = Editor;
window.TiptapStarterKit = StarterKit;
window.TiptapLink = Link;
window.TiptapUnderline = Underline;
window.TiptapTextAlign = TextAlign;
window.TiptapHighlight = Highlight;
window.TiptapFormEmbed = FormEmbed;

Alpine.start();
