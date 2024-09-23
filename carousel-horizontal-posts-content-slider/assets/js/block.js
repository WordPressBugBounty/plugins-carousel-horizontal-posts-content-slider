/**
 * Gutenberg integration
 */
;(function (blocks, element) {
  registerAllPluginBlocks()

  function registerAllPluginBlocks() {
    var chpcsPluginsData = JSON.parse(chpcs_obj_translate.blocks)
    if (!chpcsPluginsData) {
      return
    }

    for (var pluginId in chpcsPluginsData) {
      if (!chpcsPluginsData.hasOwnProperty(pluginId)) {
        continue
      }

      if (!chpcsPluginsData[pluginId].inited) {
        chpcsPluginsData[pluginId].inited = true
        registerPluginBlock(
          blocks,
          element,
          pluginId,
          chpcsPluginsData[pluginId]
        )
      }
    }
  }

  function registerPluginBlock(blocks, element, pluginId, pluginData) {
    var el = element.createElement

    var RichText = wp.editor.RichText

    var isPopup = pluginData.isPopup

    var iconEl = el('img', {
      width: pluginData.iconSvg.width,
      height: pluginData.iconSvg.height,
      src: pluginData.iconSvg.src,
    })

    blocks.registerBlockType(pluginId, {
      title: pluginData.title,
      icon: iconEl,
      category: 'common',
      attributes: {
        shortcode: {
          type: 'string',
        },
        popupOpened: {
          type: 'boolean',
          value: true,
        },
        notInitial: {
          type: 'boolean',
        },
        shortcode_id: {
          type: 'string',
        },
      },

      edit: function (props) {
        if (!props.attributes.notInitial) {
          props.setAttributes({
            notInitial: true,
            popupOpened: true,
          })

          return el('p')
        }

        if (props.attributes.popupOpened) {
          if (isPopup) {
            return showPopup(
              props.attributes.shortcode,
              props.attributes.shortcode_id
            )
          } else {
            return showShortcodeList(props.attributes.shortcode)
          }
        }

        if (props.attributes.shortcode) {
          return showShortcode()
        } else {
          return showShortcodePlaceholder()
        }

        function showPopup(shortcode, shortcode_id) {
          var shortcodeCbName = generateUniqueCbName(pluginId)
          /* store shortcode attribute into a global variable to get it from an iframe. */
          window[shortcodeCbName + '_shortcode'] = shortcode ? shortcode : ''
          window[shortcodeCbName] = function (shortcode, shortcode_id) {
            delete window[shortcodeCbName]

            if (props) {
              props.setAttributes({
                shortcode: shortcode,
                shortcode_id: shortcode_id,
                popupOpened: false,
              })
            }
          }
          props.setAttributes({ popupOpened: true })
          if (!shortcode_id && undefined != shortcode) {
            var shortcode_extract = shortcode.split(' ')
            for (i = 0; i < shortcode_extract.length; i++) {
              var attributes = shortcode_extract[i].split('=')
              if ('id' == attributes[0]) {
                shortcode_id = attributes[1].replace(/"/g, '')
              }
            }
          }
          var elem = el(
            'form',
            { className: 'chpcs-container' },
            el(
              'div',
              {
                className:
                  'chpcs-container-wrap' +
                  (pluginData.containerClass
                    ? ' ' + pluginData.containerClass
                    : ''),
              },
              el(
                'span',
                {
                  className: 'media-modal-close',
                  onClick: close,
                },
                el('span', { className: 'media-modal-icon' })
              ),
              el('iframe', {
                src:
                  pluginData.data.shortcodeUrl +
                  '&callback=' +
                  shortcodeCbName +
                  '&edit=' +
                  shortcode_id,
              })
            )
          )
          return elem
        }

        function showShortcodeList(shortcode) {
          props.setAttributes({ popupOpened: true })
          var children = []
          var shortcodeList = JSON.parse(pluginData.data)
          shortcodeList.inputs.forEach(function (inputItem) {
            if (inputItem.type === 'select') {
              children.push(
                el(
                  'option',
                  { value: '', dataId: 0 },
                  chpcs_obj_translate.empty_item
                )
              )
              if (inputItem.options.length) {
                inputItem.options.forEach(function (optionItem) {
                  var shortcode =
                    '[' +
                    shortcodeList.shortcode_prefix +
                    ' ' +
                    inputItem.shortcode_attibute_name +
                    '="' +
                    optionItem.ID +
                    '"]'
                  children.push(
                    el(
                      'option',
                      { value: shortcode, dataId: optionItem.ID },
                      optionItem.post_title
                    )
                  )
                })
              }
            }
          })

          if (shortcodeList.shortcodes) {
            shortcodeList.shortcodes.forEach(function (shortcodeItem) {
              children.push(
                el(
                  'option',
                  { value: shortcodeItem.shortcode, dataId: shortcodeItem.ID },
                  shortcodeItem.post_title
                )
              )
            })
          }

          return el(
            'form',
            { onSubmit: chooseFromList },
            el('div', {}, pluginData.titleSelect),
            el(
              'select',
              {
                value: shortcode,
                onChange: chooseFromList,
                class: 'chpcs-gb-select',
              },
              children
            )
          )
        }

        function showShortcodePlaceholder() {
          props.setAttributes({ popupOpened: false })
          return el(
            'p',
            {
              style: {
                cursor: 'pointer',
              },

              onClick: function () {
                props.setAttributes({ popupOpened: true })
              }.bind(this),
            },
            chpcs_obj_translate.nothing_selected
          )
        }

        function showShortcode() {
          return el(
            'div',
            {
              className: props.className,
              style: {
                clear: 'both',
                width: '100%',
                display: 'block',
                'min-height': '0',
                'padding-top': '15px',
                'padding-bottom': '15px',
                border: '1px solid #000000',
              },
            },

            el('img', {
              src: pluginData.iconUrl,
              alt: pluginData.title,
              style: {
                float: 'left',
                height: '20px',
                width: '20px',
              },
            }),
            el(RichText, {
              tagName: 'span',
              style: {
                float: 'left',
                margin: '-2px 10px 0px 10px',
                'font-size': '12px',
                color: 'grey',
              },
              className: 'prtx_contact_name',
              value:
                'CHPC Slider - Click here to change the slider or view post to see the slider.',
              onClick: function () {
                props.setAttributes({ popupOpened: true })
              }.bind(this),
            }),
            el('div', {
              style: { clear: 'both' },
            })
          )
        }

        function close() {
          props.setAttributes({ popupOpened: false })
        }

        function chooseFromList(event, shortcode_id) {
          var selected = event.target.querySelector('option:checked')
          props.setAttributes({
            shortcode: selected.value,
            shortcode_id: selected.dataId,
            popupOpened: false,
          })
          event.preventDefault()
        }
      },

      save: function (props) {
        return props.attributes.shortcode
      },
    })
  }

  function generateUniqueCbName(pluginId) {
    return 'chpcs_' + pluginId
  }
})(window.wp.blocks, window.wp.element)
