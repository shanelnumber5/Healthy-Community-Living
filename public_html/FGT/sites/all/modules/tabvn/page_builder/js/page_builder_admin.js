(function ($) {

  Drupal.behaviors.page_builder_admin = {
    attach: function (context, settings) {

      // start sortable rows
      $(".page-builder-rows-items-wrapper").sortable({
        items: ".page-builder-row-item",
        placeholder: "ui-state-highlight",
        opacity: 1,
        axis: "y",
        handle: ".page-builder-handle.handle-row",
        update: function (event, ui) {
          var $items = $(this).find('.page-builder-row-item');
          $items.each(function () {
            var $item = $(this);

            var new_weight = $(this).index() + 1;
            $(this).find('.page-builder-row-weight').val(new_weight);
          });

        }
      });

      // end sortable rows


      // start sortable columns in columns

      $(".page-builder-row-item").sortable({
        items: ".page-builder-column-item",
        opacity: 1,
        handle: ".page-builder-handle.handle-column",
        //helper: "clone",
        update: function (event, ui) {
          _page_builder_update_rows($(this));
        }

      });
      // end sortable in columns

      // sortable elements

      $(".page-builder-column-item").sortable({
        items: ".page-builder-element-item",
        // placeholder: "ui-state-highlight",
        opacity: 1,
        axis: "y",
        // handle: ".page-builder-handle.handle-element",
        update: function (event, ui) {
          var $items = $(this).find('.page-builder-element-item');
          $items.each(function () {
            var $item = $(this);
            var new_weight = $(this).index() + 1;
            $(this).find('.page-builder-element-weight').val(new_weight);

          });
        }

      });
      // end sortable elemetns


      // add columns
      $('.page-builder-link-add-column').click(function () {
        var page_builder_row_id = $(this).parent('.page-builder-rows-links').parent('.page-builder-row-item').find('input.page-builder-row-id').val();

        $('#edit-select-row-to-add-column').val(page_builder_row_id);
        $('#edit-add-column').trigger('mousedown'); // hit button add column
      });
      // end add column

      // remove row
      $('.page-builder-link-remove-row').click(function () {
        var page_builder_row_id = $(this).parent('.page-builder-rows-links').parent('.page-builder-row-item').find('input.page-builder-row-id').val();
        $('#edit-select-row-to-remove').val(page_builder_row_id);
        $('#edit-remove-row').trigger('mousedown');

      });

      // remove column

      $('.page-builder-link-remove-column').click(function () {
        $this = $(this);
        var page_builder_row_id = $this.parent('.page-builder-column-header-item').parent('.page-builder-column-item-inner').parent('.page-builder-column-item').parent('.page-builder-columns-wrapper').parent('.page-builder-row-item').find('input.page-builder-row-id').val();
        var page_builder_column_id = $this.parent('.page-builder-column-header-item').parent('.page-builder-column-item-inner').parent('.page-builder-column-item').find('input.page-builder-column-id').val();
        $('#edit-select-column-to-remove').val(page_builder_row_id + ',' + page_builder_column_id);
        $('#edit-remove-column').trigger('mousedown');
      });


      // modal form settings

      $('.page-builder-element-item .element-title').click(function () {

        var $page_builder_element_item_clicked = $(this).parent('.fieldset-legend').parent('legend').parent('.page-builder-element-item');
        var page_builder_element_type = $page_builder_element_item_clicked.find('input.page-builder-element-type').val();

        if (page_builder_element_type == 'block') {
          $page_builder_modal = $('#page-builder-modal-block');
          $page_builder_modal.find('.page-builder-modal-title').val($page_builder_element_item_clicked.find('input.page-builder-element-title').val());
          var page_builder_default_show_title_value = $page_builder_element_item_clicked.find('.page-builder-element-show-title').val();
          if (page_builder_default_show_title_value == 1) {
            $page_builder_modal.find('.page-builder-modal-show-title').attr('checked', 'checked');
          } else {
            $page_builder_modal.find('.page-builder-modal-show-title').removeAttr('checked');
          }
        }

        if (page_builder_element_type == 'node') {
          $page_builder_modal = $('#page-builder-modal-node');
          $page_builder_modal.find('.page-builder-modal-title').val($page_builder_element_item_clicked.find('input.page-builder-element-title').val());
          var page_builder_default_show_title_value = $page_builder_element_item_clicked.find('.page-builder-element-show-title').val();
          if (page_builder_default_show_title_value == 1) {
            $page_builder_modal.find('.page-builder-modal-show-title').attr('checked', 'checked');
          } else {
            $page_builder_modal.find('.page-builder-modal-show-title').removeAttr('checked');
          }

          $page_builder_modal.find('.page-builder-modal-nid').val($page_builder_element_item_clicked.find('.page-builder-element-nid').val());
          $page_builder_modal.find('.page-builder-modal-view-mode').val($page_builder_element_item_clicked.find('.page-builder-element-node-view-mode').val());
        }
        if (page_builder_element_type == 'global_text_area') {

          $page_builder_modal = $('#page-builder-modal-global-textarea');
          $page_builder_modal.find('.page-builder-modal-title').val($page_builder_element_item_clicked.find('input.page-builder-element-title').val());
          var page_builder_default_show_title_value = $page_builder_element_item_clicked.find('.page-builder-element-show-title').val();
          if (page_builder_default_show_title_value == 1) {
            $page_builder_modal.find('.page-builder-modal-show-title').attr('checked', 'checked');
          } else {
            $page_builder_modal.find('.page-builder-modal-show-title').removeAttr('checked');
          }
          var $default_global_text_area_content = $page_builder_element_item_clicked.find('textarea.page-builder-element-global-text-area').val();
          $page_builder_modal.find('.page-builder-modal-content').val($default_global_text_area_content);
          _admin_page_builder_enable_rich_editor($default_global_text_area_content);

        }





        $page_builder_modal.dialog({
          modal: true,
          minWidth: 550,
          minHeight: 380,
          title: $page_builder_element_item_clicked.find('span.element-title').text(),
          buttons: [
            {text: "Save", click: function () {
                $this = $(this);
                block_title = $this.find('.page-builder-modal-title').val();
                if (block_title !== '') {
                  $page_builder_element_item_clicked.find('span.element-title').text(block_title);

                }

                var page_builder_show_title = 0;
                if ($this.find('.page-builder-modal-show-title').attr('checked')) {
                  page_builder_show_title = 1;
                }
                $page_builder_element_item_clicked.find('input.page-builder-element-show-title').val(page_builder_show_title);
                $page_builder_element_item_clicked.find('input.page-builder-element-title').val(block_title);
                if (page_builder_element_type == 'global_text_area') {
                  var $global_text_area_html = tinymce.activeEditor.getContent();

                  $page_builder_element_item_clicked.find('textarea.page-builder-element-global-text-area').val($global_text_area_html);
                }
                if (page_builder_element_type == 'node') {
                  $page_builder_element_item_clicked.find('input.page-builder-element-nid').val($this.find('.page-builder-modal-nid').val());
                  $page_builder_element_item_clicked.find('input.page-builder-element-node-view-mode').val($this.find('.page-builder-modal-view-mode').val());
                }

                $(this).dialog("close");
              }
            },
            {text: "Delete", click: function () {
                var page_builder_row_id = $page_builder_element_item_clicked.parents('.page-builder-row-item').find('input.page-builder-row-id').val();
                var page_builder_column_id = $page_builder_element_item_clicked.parents('.page-builder-column-item').find('input.page-builder-column-id').val();
                var page_builder_element_id = $page_builder_element_item_clicked.find('input.page_builder_element_id').val();
                $('#edit-select-element-to-remove').val(page_builder_row_id + ',' + page_builder_column_id + ',' + page_builder_element_id);
                $(this).dialog("close");
                $('#edit-remove-element').trigger('mousedown');
              }
            }
          ]

        });



      });

      // refresh elemetns

      $('.page-builder-reload-elements').click(function () {
        $('.page-builder-refresh-elements').trigger('mousedown');

      });

      // drag and drop element into column
      $("#page-builder-elements-items .page-builder-element").draggable({
        appendTo: "body",
        helper: "clone"
      });
      $(".page-builder-column-item").droppable({
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        accept: ":not(.ui-sortable-helper)",
        drop: function (event, ui) {
          $this = $(this);
          var page_builder_row_id = $this.parents('.page-builder-row-item').find('input.page-builder-row-id').val();
          var page_builder_column_id = $this.find('input.page-builder-column-id').val();
          $('#edit-select-column-to-add-element').val(page_builder_row_id + ',' + page_builder_column_id);
          var page_builder_e_data = ui.draggable.find('.page-builder-element-data').val();
          $('#edit-select-element-info').val(page_builder_e_data);
          $('#edit-add-element').trigger('mousedown');
        }
      });
      // end drag and drop elements

      // click settings row

      $('.page-builder-link-settings-row').click(function () {
        var $this_row = $(this).parent('.page-builder-rows-links').parent('.page-builder-row-item');

        $this_row.find('.page-builder-row-settings').toggle();

      });

      // resizable columns
      if ($('.page-builder-columns-wrapper').length) {

        var page_builder_row_width = 718; //$('.page-builder-columns-wrapper').width();
        var page_builder_column_resize_step = 58.1667; //(page_builder_row_width / 12);
        $('.page-builder-columns-wrapper .page-builder-column-item').resizable({
          grid: [page_builder_column_resize_step, 0],
          maxWidth: page_builder_row_width,
          minWidth: page_builder_column_resize_step,
          handles: 'e',
          containment: ".page-builder-columns-wrapper",
          animateDuration: "fast",
          // helper: "ui-resizable-helper",
          resize: function (event, ui) {
            $this = $(this);
            var page_builder_columns_after_resize = $this.width();
            var page_builder_column_size = page_builder_columns_after_resize / page_builder_column_resize_step;
            var page_builder_new_column_size = Math.round(page_builder_column_size);
            $this.find('.page_builder_column_grid_size').val(page_builder_new_column_size);
            $this.removeClass(
                    function (index, css) {
                      return (css.match(/\bpage-builder-grid-\S+/g) || []).join(' ');
                    }
            );
            $this.addClass('page-builder-grid-' + page_builder_new_column_size);
            $this.find('.page-builder-grid-size-title').text(page_builder_new_column_size + '/12');

            page_builder_column_height = $this.height();
            $this.removeAttr('style');
            $this.css('height', page_builder_column_height);
          }

        });


      }
      //

      if ($('.page-builder-column-item').length) {
        _page_builder_equal_column_height($('.page-builder-row-item'));
      }
      function _page_builder_update_rows(page_builder_rows_wrapper) {

        var $items = page_builder_rows_wrapper.find('.page-builder-column-item');
        $items.each(function () {
          var $item = $(this);
          var new_weight = $(this).index() + 1;
          $(this).find('.page-builder-column-weight').val(new_weight);

        });
      }

      function _page_builder_equal_column_height(group) {

        group.each(function () {
          $this = $(this);
          columns = $this.find('.page-builder-column-item');
          tallest = 0;
          columns.each(function () {
            thisHeight = $(this).height();
            if (thisHeight > tallest) {
              tallest = thisHeight;
            }
          });
          $this.find('.page-builder-column-item').css('height', tallest);

        });

      }

      _admin_page_builder_filter_list_init();

    }
  };

  function _admin_page_builder_enable_rich_editor(data) {
    tinymce.init({
      selector: "textarea.page-builder-modal-content",
      /*plugins: [
       "advlist autolink lists link image charmap print preview anchor",
       "searchreplace visualblocks code fullscreen",
       "insertdatetime media table contextmenu paste textcolor colorpicker"
       ],*/
      //valid_elements : "*",
      extended_valid_elements: "i[class|id]",
      file_browser_callback: function (field_name, url, type, win) {
        // win.document.getElementById(field_name).value = 'my browser value';
        var imce_url = Drupal.settings.page_builder_editor.imce_url;
        if (imce_url && win !== 'undefined') {

          win.open(Drupal.settings.page_builder_editor.imce_url + encodeURIComponent(field_name), '', 'width=760,height=560,resizable=1');
        } else {
          alert('Install module Imce http://drupal.org/project/imce to use this featured.');

        }
      },
      file_browser_callback_types: 'file image media',
      plugins: "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
      theme: "advanced",
      theme_advanced_resizing: true,
    });
    if (!(tinymce.activeEditor == null)) {
      tinymce.activeEditor.setContent(data);
    }

  }
  function _admin_page_builder_filter_list_init() {
    var $list_wrapper = $('#page-builder-filter-list');
    $list_wrapper.find('li').removeClass('element-hidden');
    $list_wrapper.find('input.search').keyup(function () {

      _admin_page_builder_filter_actions($(this), $list_wrapper);
    });
    $list_wrapper.find('input.search').keypress(function () {
      _admin_page_builder_filter_actions($(this), $list_wrapper);
    });
  }

  function _admin_page_builder_filter_actions($this, $list_wrapper) {
    $list_wrapper.addClass('list-searching');
    $list_wrapper.find('.page-builder-element').removeClass('element-matching');
    var $search_string = $this.val();
    $list_wrapper.find('h3.element-title').each(function () {
      var $this_item = $(this);
      var $element_title_value = $this_item.html();

      if ($search_string.length > 0 && $element_title_value.toLowerCase().indexOf($search_string.toLowerCase()) >= 0) {

        $this_item.parents('.page-builder-element').addClass('element-matching');
      }
      if ($search_string.length === 0) {
        $list_wrapper.removeClass('list-searching');
        $list_wrapper.find('.page-builder-element').removeClass('element-matching');
      }
    });
  }

  $(document).ready(function () {
    _admin_page_builder_filter_list_init();
    var $sidebar = $("#page-builder-elements-items"),
            $window = $(window),
            offset = $sidebar.offset(),
            topPadding = 30;

    $window.scroll(function () {
      if ($window.scrollTop() > offset.top) {
        $sidebar.stop().animate({
          marginTop: $window.scrollTop() - offset.top + topPadding
        });
      } else {
        $sidebar.stop().animate({
          marginTop: 0
        });
      }
    });
  });






})(jQuery);

