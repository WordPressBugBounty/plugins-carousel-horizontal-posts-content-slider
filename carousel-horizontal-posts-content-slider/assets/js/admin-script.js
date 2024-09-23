jQuery(document).ready(function () {
  //date picker start and end date
  jQuery('#start_date').datepicker()
  jQuery('#end_date').datepicker()

  jQuery('.post-type-wa_chpcs')
    .find("[name='post_title']")
    .prop('required', true)

  //select2 sortable
  var selectEl = jQuery('#wa_chpcs_contents_order').select2({ tags: true })
  selectEl
    .next()
    .children()
    .children()
    .children()
    .sortable({
      containment: 'parent',
      stop: function (event, ui) {
        ui.item
          .parent()
          .children('[title]')
          .each(function () {
            var title = jQuery(this).attr('title')
            var original = jQuery(
              'option:contains(' + title + ')',
              selectEl
            ).first()
            original.detach()
            selectEl.append(original)
          })
        selectEl.change()
      },
    })

  //font colour
  jQuery('#wa_chpcs_font_colour').spectrum({
    showAlpha: true,
    showInput: true,
    preferredFormat: 'rgb',
    move: function (c) {
      jQuery(this).val(c.toRgbString())
    },
  })

  //direction arrows colour
  jQuery('#wa_chpcs_control_colour').spectrum({
    showAlpha: true,
    showInput: true,
    preferredFormat: 'rgb',
    move: function (c) {
      jQuery(this).val(c.toRgbString())
    },
  })

  jQuery('#wa_chpcs_image_hover_colour').spectrum({
    showAlpha: true,
    showInput: true,
    preferredFormat: 'rgb',
    move: function (c) {
      jQuery(this).val(c.toRgbString())
    },
  })

  jQuery('#wa_chpcs_control_bg_colour').spectrum({
    showAlpha: true,
    showInput: true,
    preferredFormat: 'rgb',
    move: function (c) {
      jQuery(this).val(c.toRgbString())
    },
  })

  jQuery('#wa_chpcs_control_hover_colour').spectrum({
    showAlpha: true,
    showInput: false,
    preferredFormat: 'rgb',
    move: function (c) {
      jQuery(this).val(c.toRgbString())
    },
  })

  //slides
})

//

jQuery(document).ready(function () {
  is_slide_add_image = false

  function update_order_numbers() {
    jQuery('.slides').each(function () {
      jQuery(this)
        .children('.slide')
        .each(function (i) {
          jQuery(this)
            .find('td.slide_order .circle')
            .first()
            .html(i + 1)
        })
    })
  }

  function update_slide_order_field() {
    jQuery('#unique_slides_order').val(
      jQuery('.slides').sortable('toArray').toString()
    )
  }

  function current_selected_slide_id() {
    return window.selected_slide_id
  }

  function update_selected_slide_id(id) {
    window.selected_slide_id = id
  }

  function insert_into_editor(html) {
    var b,
      a = typeof tinymce != 'undefined',
      f = typeof QTags != 'undefined'
    if (!wpActiveEditor) {
      if (a && tinymce.activeEditor) {
        b = tinymce.activeEditor
        wpActiveEditor = b.id
      } else {
        if (!f) {
          return false
        }
      }
    } else {
      if (a) {
        if (
          tinymce.activeEditor &&
          (tinymce.activeEditor.id == 'mce_fullscreen' ||
            tinymce.activeEditor.id == 'wp_mce_fullscreen')
        ) {
          b = tinymce.activeEditor
        } else {
          b = tinymce.get(wpActiveEditor)
        }
      }
    }
    if (b && !b.isHidden()) {
      if (tinymce.isIE && b.windowManager.insertimagebookmark) {
        b.selection.moveToBookmark(b.windowManager.insertimagebookmark)
      }
      if (html.indexOf('[caption') === 0) {
        if (b.wpSetImgCaption) {
          html = b.wpSetImgCaption(c)
        }
      } else {
        if (html.indexOf('[gallery') === 0) {
          if (b.plugins.wpgallery) {
            html = b.plugins.wpgallery._do_gallery(c)
          }
        } else {
          if (html.indexOf('[embed') === 0) {
            if (b.plugins.wordpress) {
              html = b.plugins.wordpress._setEmbed(c)
            }
          }
        }
      }
      b.execCommand('mceInsertContent', false, html)
    } else {
      if (f) {
        QTags.insertContent(c)
      } else {
        document.getElementById(wpActiveEditor).value += c
      }
    }
  }

  jQuery('#submitslider #delete-action a').click(function (e) {
    if (!confirm('Move to Trash. Are you sure ?')) return false
  })

  jQuery('body').on('click', '.delete_slide', function (e) {
    if (!confirm('Delete slide. Are you sure ?')) return false

    var selector = jQuery('.slide#' + jQuery(this).data('id'))

    selector.fadeOut('slow', function () {
      selector.remove()
      update_order_numbers()
      jQuery('.slide_meta td.slide_order').mouseover()
      update_slide_order_field()
    })
  })

  jQuery('body').on('click', '.edit_slide', function () {
    var edit_form = jQuery(
      '.slide#' + jQuery(this).data('id') + ' .slide_form_mask'
    )

    if (edit_form.css('display') == 'none') {
      jQuery('.slide#' + jQuery(this).data('id')).addClass('form_open')

      edit_form.slideDown()
      update_selected_slide_id(jQuery(this).data('id'))
    } else {
      jQuery('.slide#' + jQuery(this).data('id')).removeClass('form_open')

      edit_form.slideUp()
      update_selected_slide_id(null)
    }
  })

  jQuery('#add_slide_button').click(function () {
    jQuery('.slide_image_preview').hide()

    jQuery('.no_slides_message').hide()
    var slide_id = jQuery('#next_slide_id').val()
    var label_name_attr = 'slides[' + slide_id + '][label]'
    var textarea_name_attr = 'slides[' + slide_id + '][textarea]'
    var url_name_attr = 'slides[' + slide_id + '][slide_url]'
    var radio_name_attr = 'slides[' + slide_id + '][radio]'
    var bg_colour_attr = 'slides[' + slide_id + '][bg_colour]'
    var font_colour_attr = 'slides[' + slide_id + '][font_colour]'
    var image_name_attr = 'slides[' + slide_id + '][image]'
    var type_name_attr = 'slides[' + slide_id + '][type]'
    var attachment_name_attr = 'slides[' + slide_id + '][attachment]'
    var html_name_attr = 'slides[' + slide_id + '][html]'

    var slide_video_type_name_attr =
      'slides[' + slide_id + '][slide_video_type]'
    var slide_auto_play_name_attr = 'slides[' + slide_id + '][slide_auto_play]'
    var slide_video_id_name_attr = 'slides[' + slide_id + '][slide_video_id]'

    var allow_full_screen_name_attr =
      'slides[' + slide_id + '][allow_full_screen]'
    var slide_url_target_attr = 'slides[' + slide_id + '][url_target]'

    jQuery('#new_slide_template .allow_full_screen').attr(
      'name',
      allow_full_screen_name_attr
    )
    jQuery('#new_slide_template .url_target').attr(
      'name',
      slide_url_target_attr
    )

    jQuery('#new_slide_template .allow_full_screen').attr(
      'data-id',
      allow_full_screen_name_attr
    )
    jQuery('#new_slide_template .url_target').attr(
      'data-id',
      slide_url_target_attr
    )

    jQuery('#new_slide_template .slide_video_type').attr(
      'name',
      slide_video_type_name_attr
    )
    jQuery('#new_slide_template .auto_play').attr(
      'name',
      slide_auto_play_name_attr
    )
    jQuery('#new_slide_template .video_id').attr(
      'name',
      slide_video_id_name_attr
    )

    jQuery('#new_slide_template .slide_video_type').attr(
      'data-id',
      slide_video_type_name_attr
    )
    jQuery('#new_slide_template .auto_play').attr(
      'data-id',
      slide_auto_play_name_attr
    )
    jQuery('#new_slide_template .video_id').attr(
      'data-id',
      slide_video_id_name_attr
    )

    jQuery('#new_slide_template .slide').attr('id', slide_id)
    jQuery('#new_slide_template .edit_slide').attr('data-id', slide_id)
    jQuery('#new_slide_template .add_image').attr('data-id', slide_id)
    jQuery('#new_slide_template .delete_slide').attr('data-id', slide_id)
    jQuery('#new_slide_template .slide_order span.circle').attr(
      'data-id',
      slide_id
    )
    jQuery('#new_slide_template .slide_edit_close .edit_slide').attr(
      'data-id',
      slide_id
    )
    jQuery('#new_slide_template .slide_label_input').attr('data-id', slide_id)
    jQuery('#new_slide_template .slide_type select').attr('data-id', slide_id)
    jQuery('#new_slide_template .slide_label_input').attr(
      'name',
      label_name_attr
    )
    jQuery('#new_slide_template .slide_url_input').attr('data-id', slide_id)
    jQuery('#new_slide_template .slide_url_input').attr('name', url_name_attr)
    jQuery('#new_slide_template .slide_textarea_input').attr(
      'data-id',
      slide_id
    )
    jQuery('#new_slide_template .slide_textarea_input').attr(
      'name',
      textarea_name_attr
    )
    jQuery('#new_slide_template .slide_radio_input').attr('data-id', slide_id)
    jQuery('#new_slide_template .slide_radio_input').attr(
      'name',
      radio_name_attr
    )
    jQuery('#new_slide_template .slide_bg_colour_input').attr(
      'data-id',
      slide_id
    )
    jQuery('#new_slide_template .slide_bg_colour_input').attr(
      'name',
      bg_colour_attr
    )
    jQuery('#new_slide_template .slide_font_colour_input').attr(
      'data-id',
      slide_id
    )
    jQuery('#new_slide_template .slide_font_colour_input').attr(
      'name',
      font_colour_attr
    )
    jQuery('#new_slide_template .slide_type select').attr(
      'name',
      type_name_attr
    )
    jQuery('#new_slide_template .slide_attachment').attr(
      'name',
      attachment_name_attr
    )
    jQuery('#new_slide_template .slide_html textarea').attr(
      'name',
      html_name_attr
    )
    var template = jQuery('#new_slide_template').html()
    jQuery('.slides').append(template)

    update_order_numbers()
    update_selected_slide_id(slide_id)

    jQuery('#new_slide_template .slide_label_input').attr('name', '')
    jQuery('#new_slide_template .slide_textarea_input').attr('name', '')
    jQuery('#new_slide_template .slide_type').attr('name', '')
    jQuery('#new_slide_template .slide_attachment').attr('name', '')
    jQuery('#new_slide_template .slide_type select').attr('name', '')
    jQuery('#new_slide_template .slide_html textarea').attr('name', '')
    jQuery('#new_slide_template .slide_radio_input').attr('name', '')
    jQuery('#new_slide_template .slide_bg_colour_input').attr('name', '')
    jQuery('#new_slide_template .slide_font_colour_input').attr('name', '')
    jQuery('#new_slide_template .slide_url_input').attr('name', '')
    jQuery('#new_slide_template .slide_video_type').attr('name', '')
    jQuery('#new_slide_template .auto_play').attr('name', '')
    jQuery('#new_slide_template .video_id').attr('name', '')
    jQuery('#new_slide_template .allow_full_screen').attr('name', '')
    jQuery('#new_slide_template .url_target').attr('name', '')
    jQuery('#next_slide_id').val(Number(jQuery('#next_slide_id').val()) + 1)
  })

  jQuery('body').on('mouseover', '.slide_meta td.slide_order', function () {
    var slides = jQuery(this).closest('.slides')

    if (slides.hasClass('sortable')) return false

    slides.addClass('sortable').sortable({
      update: function (event, ui) {
        update_order_numbers()
        update_slide_order_field()
      },
      handle: 'td.slide_order',
      cursor: 'move',
      axis: 'y',
      revert: true,
    })
  })

  jQuery('.body').on('hover', '.slide_label', function () {
    jQuery(this).parent().find('.row_options').show()
  })

  jQuery('.body').on('mouseleave', '.slide_label', function () {
    jQuery(this).parent().find('.row_options').hide()
  })

  jQuery('body').on('click', '.add_image', function () {
    var image_input = jQuery(this).parent().find('.slide_image_input')
    var image_preview = jQuery(this).parent().find('.slide_image_preview img')

    if (image_input.val() !== jQuery(image_preview).attr('src')) {
      jQuery(image_preview).attr('src', image_input.val())

      return false
    }

    update_selected_slide_id(jQuery(this).data('id'))

    is_slide_add_image = true

    tb_show(
      'Add Image',
      'media-upload.php?referer=post.php&post_id=0&slider_id=' +
        jQuery('#post_ID').val() +
        '&type=image&TB_iframe=true',
      false
    )

    return false
  })

  window.send_to_editor = function (html) {
    if (is_slide_add_image) {
      var image_url = jQuery('img', html).attr('src')

      if (image_url === undefined) image_url = jQuery(html).attr('src')

      classes = jQuery('img', html).attr('class')

      if (classes === undefined) classes = jQuery(html).attr('class')

      id = classes.replace(/(.*?)wp-image-/, '')

      var image_input =
        '.slide#' + current_selected_slide_id() + ' .slide_image_input'
      var image_preview =
        '.slide#' + current_selected_slide_id() + ' .slide_image_preview img'
      var attachment_input =
        '.slide#' + current_selected_slide_id() + ' .slide_attachment'

      jQuery(image_input).val(image_url)
      jQuery(image_preview).attr('src', image_url)

      jQuery(attachment_input).val(id)

      is_slide_add_image = false
    } else {
      insert_into_editor(html)
    }
    tb_remove()
  }

  jQuery('body').on('change', '.slide_label_input', function () {
    label = jQuery(
      '.slide#' + jQuery(this).data('id') + ' .slide_label strong a'
    )

    label.text(jQuery(this).val())
  })

  jQuery('body').on('change', '.slide_textarea_input', function () {
    label = jQuery(
      '.slide#' + jQuery(this).data('id') + ' .slide_textarea strong a'
    )

    label.text(jQuery(this).val())
  })

  jQuery('body').on('change', '.slide_type select', function () {
    image = jQuery('.slide#' + jQuery(this).data('id') + ' tr.slide_image')
    slide_url = jQuery('.slide#' + jQuery(this).data('id') + ' tr.slide_url')
    slide_font_color = jQuery(
      '.slide#' + jQuery(this).data('id') + ' tr.slide_font_color'
    )
    slide_textarea_input = jQuery(
      '.slide#' + jQuery(this).data('id') + ' tr.slide_caption'
    )
    slide_positioning = jQuery(
      '.slide#' + jQuery(this).data('id') + ' tr.slide_positioning'
    )
    slide_bg_color = jQuery(
      '.slide#' + jQuery(this).data('id') + ' tr.slide_bg_color'
    )
    url_target = jQuery(
      '.slide#' + jQuery(this).data('id') + ' tr.slide_url_target'
    )

    html = jQuery('.slide#' + jQuery(this).data('id') + ' tr.slide_html')

    if (jQuery(this).val() == 'image') {
      image.show()

      slide_url.show()
      //   slide_font_color.show();
      //   slide_positioning.show();
      //  slide_bg_color.show();
      slide_textarea_input.show()
      url_target.show()

      html.hide()
      return
    }

    return
  })

  jQuery('.slides .slide_type select').each(function (index, elem) {
    image = jQuery('.slide#' + jQuery(this).data('id') + ' tr.slide_image')
    html = jQuery('.slide#' + jQuery(this).data('id') + ' tr.slide_html')

    slide_url = jQuery('.slide#' + jQuery(this).data('id') + ' tr.slide_url')
    slide_font_color = jQuery(
      '.slide#' + jQuery(this).data('id') + ' tr.slide_font_color'
    )
    slide_positioning = jQuery(
      '.slide#' + jQuery(this).data('id') + ' tr.slide_positioning'
    )
    slide_bg_color = jQuery(
      '.slide#' + jQuery(this).data('id') + ' tr.slide_bg_color'
    )
    slide_textarea_input = jQuery(
      '.slide#' + jQuery(this).data('id') + ' tr.slide_caption'
    )
    url_target = jQuery(
      '.slide#' + jQuery(this).data('id') + ' tr.slide_url_target'
    )

    if (jQuery(this).val() == 'image') {
      image.show()
      slide_url.show()
      slide_textarea_input.show()
      url_target.show()
      html.hide()
      return
    }

    return
  })

  jQuery('#shortcode_text_input').click(function () {
    jQuery(this).select()
  })

  if (pagenow == 'edit-wa-chpcs_slider') {
    jQuery('.add-new-h2').addClass('wa-chpcs-button')
    // (function(jQuery){
    jQuery('#wpbody .wrap').wrapInner('<div id="wa-chpcs-col-left" />')
    jQuery('#wpbody .wrap').wrapInner('<div id="wa-chpcs-cols" />')
    jQuery('#wa-chpcs-col-right')
      .removeClass('hidden')
      .prependTo('#wa-chpcs-cols')

    jQuery('#wa-chpcs-col-left > .icon32').insertBefore('#wa-chpcs-cols')
    jQuery('#wa-chpcs-col-left > h2').insertBefore('#wa-chpcs-cols')
    //  })(jQuery);
  }
  if (pagenow == 'wa-chpcs_slider') jQuery('.add-new-h2').hide()
})
