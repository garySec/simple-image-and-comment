import { Controller } from 'stimulus';

var $collectionHolder;

var $addNewItem = $('<a href="#" class="btn btn-info m-3">Add new item</a>');

$(document).ready(function () {

    $collectionHolder = $('#cmt_id');

    $collectionHolder.append($addNewItem);

    $collectionHolder.data('index', $collectionHolder.find('.card').length)

    $collectionHolder.find('.card').each(function () {

        addRemoveButton($(this));
    });

    $addNewItem.click(function (e) {

        e.preventDefault();

        addNewForm();
    })
});

/*
 * creates a new form and appends it to the collectionHolder
 */
function addNewForm() {

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var newForm = prototype;

    newForm = newForm.replace(/__name__/g, index);

    $collectionHolder.data('index', index+1);

    var $card = $('<div class="card m-3"><div class="card-header bg-dark text-white">Comment</div></div>');

    var $cardBody = $('<div class="card-body"></div>').append(newForm);

    $card.append($cardBody);

    addRemoveButton($card);

    $addNewItem.before($card);
}

function addRemoveButton ($card) {

    var $removeButton = $('<a href="#" class="btn btn-danger">Remove</a>');

    var $cardFooter = $('<div class="card-footer"></div>').append($removeButton);

    $removeButton.click(function (e) {
        e.preventDefault();

        $(e.target).parents('.card').slideUp(500, function () {
            $(this).remove();
        })
    });

    $card.append($cardFooter);
}