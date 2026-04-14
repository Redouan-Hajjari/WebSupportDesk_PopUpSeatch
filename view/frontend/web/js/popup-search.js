define([
    'jquery'
], function ($) {
    'use strict';

    return function (config) {
        var $popup = $('#wsd-popup-search');
        var $input = $('#wsd-popup-search-input');
        var $results = $('#wsd-popup-search-results');
        var suggestUrl = (config && config.suggestUrl) ? config.suggestUrl : '';
        var placeholderImage = (config && config.placeholderImage) ? config.placeholderImage : '';
        var timer = null;

        if (!$popup.length || !suggestUrl) {
            return;
        }

        function openPopup() {
            if ($popup.hasClass('is-open')) {
                return;
            }
            $popup.addClass('is-open').attr('aria-hidden', 'false');
            $('body').addClass('wsd-popup-search-open');
            window.setTimeout(function () {
                $input.trigger('focus');
            }, 80);
        }

        function closePopup() {
            $popup.removeClass('is-open').attr('aria-hidden', 'true');
            $('body').removeClass('wsd-popup-search-open');
            $input.val('');
            $results.empty();
        }

        function renderItems(items) {
            var html = '';
            var imageUrl = '';

            function esc(str) {
                return String(str || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            if (!items || !items.length) {
                $results.html('<div class="wsd-popup-search__empty">No results found.</div>');
                return;
            }

            items.forEach(function (item) {
                imageUrl = item.image ? String(item.image) : String(placeholderImage || '');
                html += '<a class="wsd-popup-search__item" href="' + esc(item.url || '#') + '">';
                html += '<img class="wsd-popup-search__thumb" src="' + esc(imageUrl) + '" alt="">';
                html += '<span class="wsd-popup-search__meta">';
                html += '<span class="wsd-popup-search__name">' + esc(item.name || '') + '</span>';
                html += '<span class="wsd-popup-search__price">' + esc(item.price || '') + '</span>';
                html += '</span>';
                html += '</a>';
            });

            $results.html(html);
        }

        function searchNow(value) {
            if (!value || value.length < 2) {
                $results.empty();
                return;
            }

            $.getJSON(suggestUrl, {q: value}).done(function (response) {
                renderItems((response && response.items) ? response.items : []);
            }).fail(function () {
                $results.html('<div class="wsd-popup-search__empty">Search failed. Try again.</div>');
            });
        }

        $(document).on('click', '.block-search .action.search, .block-search .label, .block-search .control button', function (e) {
            e.preventDefault();
            openPopup();
        });

        $(document).on('focusin click', '.block-search input[type="text"], .block-search input#search', function () {
            openPopup();
        });

        $popup.on('click', '[data-role="popup-close"], [data-role="popup-backdrop"]', function () {
            closePopup();
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $popup.hasClass('is-open')) {
                closePopup();
            }
        });

        $input.on('input', function () {
            var value = $.trim($(this).val());
            window.clearTimeout(timer);
            timer = window.setTimeout(function () {
                searchNow(value);
            }, 250);
        });

        $popup.on('click', '.wsd-popup-search__panel', function (e) {
            e.stopPropagation();
        });
    };
});

