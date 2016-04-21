// ------------------------- DTO -------------------------  //

function load() {
    var that = this;
    jQuery.getJSON(this.uri, function(data) {
        that.data = data;
        $(that).trigger('data:loaded');
    });
};

/**
 * LoadableObject - common parent for DTO objects
 * @constructor
 */
function LoadableObject() {
    this.data = [];
    this.load = load;
    this.get = function() {
        return this.data;
    }
}

/**
 * Restaurants DTO
 * @constructor
 */
function Restaurants() {
    this.uri = 'http://localhost:8080/api/v1/restaurants';

    this.get = function(id) {
        if (this.data[id])
          return this.data[id];
        return undefined;
    }
}
Restaurants.prototype = new LoadableObject();

/**
 * Menu
 * @param restaurant
 * @constructor
 */
function Menu(restaurant) {

    this.uri = 'http://localhost:8080/api/v1/restaurants/' + restaurant + '/menu';
    this.uriDelete = this.uri + '/delete';

    // postprocess loaded data
    $(this).on('data:loaded', function() {
        this.data = this.data.data.attributes.content;
    });
}
Menu.prototype = new LoadableObject();

// ------------------------- VIEW ------------------------- //

/**
 * @param restaurants
 * @constructor
 */
function RestaurantsView(restaurants) {

    this.restaurants = restaurants;
    this.element = 'select#restaurant';

    this.init = function () {
        $(this.restaurants).on('data:loaded', this.view());
        $(this.element).change(this.change());
        this.restaurants.load();
        (this.change())();
    };

    // Do not use directly! It's a handler.
    this.view = function() {
        var that = this;
        return function (event) {
            var options = [];
            for (id in that.restaurants.data) {
                var option = document.createElement('option');
                option.value = id;
                option.innerHTML = that.restaurants.data[id];
                options.push(option);
            }
            $(that.element).append(options);
        }
    };

    this.change = function() {
        var that = this;
        return function(event) {
            new MenuView(that.restaurants.get(this.value));
        }
    }
}

/**
 * @param restaurant
 * @constructor
 */
function MenuView(restaurant) {

    this.restaurant = restaurant;
    this.menu = new Menu(restaurant);
    this.element = '#menu';

    this.init = function() {
        if (this.restaurant == undefined) {
            this.undefined();
            return;
        }
        $(this.menu).on('data:loaded', this.view());
        this.menu.load();
    };

    this.view = function() {
        var that = this;
        return function(event) {
            var table = document.createElement('table');
            for (type in this.data) {
                $(table).append('<tr><th colspan="2">' + type + '</th></tr>');
                for (dish in this.data[type])
                    $(table).append('<tr><td>'
                        + this.data[type][dish][0] + '</td><td>'
                        + this.data[type][dish][1] + '</td></tr>');
            }
            var h2 = document.createElement('h2')
            h2.innerHTML = that.restaurant;
            var div = document.createElement('div');
            $(that.element)
                .empty()
                .append(h2)
                .append(
                    $(div).addClass('item').append(table)
                )
            ;
        }
    };

    this.undefined = function() {
        $(this.element)
            .empty().append('<p>Menu is not available. Plese select a restaurant.</p>');
    }

    this.init();
}

var restaurantsView = new RestaurantsView(new Restaurants());

$(document).ready(function() {
        restaurantsView.init();
    }
);