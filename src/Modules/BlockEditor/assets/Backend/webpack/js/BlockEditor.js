import EditorJS from '@editorjs/editorjs'
import Embed from '@editorjs/embed'
import Header from '@editorjs/header'
import List from '@editorjs/list'
import Paragraph from '@editorjs/paragraph'
import Underline from '@editorjs/underline'
import Button from './Blocks/Button'
import Quote from './Blocks/Quote'
import Raw from './Blocks/Raw'
import TextImageBlock from './Blocks/TextImageBlock'
import TextColumnBlock from './Blocks/TextColumnBlock'
import { createApp } from 'vue'
import TestComponent from '../../../../../Backend/assets/Backend/webpack/js/Components/TestComponent.vue'
import Table from '@editorjs/table'
const AlignmentTuneTool = require('editorjs-text-alignment-blocktune')
const ColorPlugin = require('editorjs-text-color-plugin')

export class BlockEditor {
  constructor () {
    this.initEditors($('textarea[data-fork-block-editor-config]'))
    this.loadEditorsInCollections()
  }

  initEditors (editors) {
    if (editors.length > 0) {
      editors.each((index, editor) => {
        this.createEditor($(editor))
      })
    }
  }

  createEditor ($element) {
    BlockEditor.fromJson($element, $element.attr('data-fork-block-editor-config'))
  }

  loadEditorsInCollections () {
    $('[data-addfield="collection"]').on('collection-field-added', (event, formCollectionItem) => {
      this.initEditors($(formCollectionItem).find('textarea[data-fork-block-editor-config]'))
    })
  }

  static getClassFromVariableName (string) {
    let scope = window
    const scopeSplit = string.split('.')
    let i

    for (i = 0; i < scopeSplit.length - 1; i++) {
      scope = scope[scopeSplit[i]]

      if (scope === undefined) return
    }

    return scope[scopeSplit[scopeSplit.length - 1]]
  }

  static fromJson ($element, jsonConfig) {
    const config = JSON.parse(jsonConfig)
    for (const name of Object.keys(config)) {
      config[name].class = BlockEditor.getClassFromVariableName(config[name].class)
    }

    BlockEditor.create($element, config)
  }

  static create ($element, tools) {
    $element.hide()
    const editorId = $element.attr('id') + '-block-editor'
    $element.after('<div id="' + editorId + '"></div>')
    tools.alignmentBlockTune = {
      class: AlignmentTuneTool,
      config: {
        default: 'left'
      }
    }
    // Uncomment following block to enable text color plugin. This is an inline menu tool and can be disabled for select blocks by defining the inline menu in the block config (in PHP file)
    // tools.textColor = {
    //   class: ColorPlugin,
    //   config: {
    //     colorCollections: ['#EC7878', '#9C27B0', '#673AB7', '#3F51B5', '#0070FF', '#03A9F4', '#00BCD4', '#4CAF50', '#8BC34A', '#CDDC39', '#FFF'],
    //     defaultColor: '#000',
    //     type: 'text',
    //     customPicker: true // add a button to allow selecting any colour
    //   }
    // }

    let data = {}
    try {
      data = JSON.parse($element.text())
    } catch (e) {
      // ignore the current content since we can't decode it
    }

    const editor = new EditorJS({
      holder: editorId,
      inlineToolbar: true,
      data,
      onReady: () => {
        // initialize vue app to enable media library image selector in editor js
        const vueApp = document.querySelector('[data-role="vue-app"]')
        if (vueApp) {
          const app = createApp()
          // global component
          app.component('TestComponent', TestComponent)
          app.mount('.vue-app')
        }
      },
      onChange: () => {
        editor.save().then((outputData) => {
          console.log('outputData: ', outputData)
          $element.val(JSON.stringify(outputData))
        }).catch((error) => {
          console.debug('Saving failed: ', error)
        })
      },
      tools
    })
  }
}

$(window).on('load', () => {
  if (window.BlockEditor === undefined) {
    window.BlockEditor = { blocks: {} }
  }

  if (window.BlockEditor.blocks === undefined) {
    window.BlockEditor.blocks = {}
  }

  window.BlockEditor.editor = BlockEditor
  window.BlockEditor.blocks.Header = Header
  window.BlockEditor.blocks.Embed = Embed
  window.BlockEditor.blocks.List = List
  window.BlockEditor.blocks.Paragraph = Paragraph
  window.BlockEditor.blocks.Underline = Underline
  window.BlockEditor.blocks.Button = Button
  window.BlockEditor.blocks.Quote = Quote
  window.BlockEditor.blocks.Raw = Raw
  window.BlockEditor.blocks.TextImageBlock = TextImageBlock
  window.BlockEditor.blocks.TextColumnBlock = TextColumnBlock
  window.BlockEditor.blocks.Table = Table

  window.backend.blockEditor = new BlockEditor()
})