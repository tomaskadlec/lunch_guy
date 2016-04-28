// ------------------------- Service -------------------------  //

function load() {
    var that = this;
    jQuery.getJSON(this.uri, function(data) {
        that.data = data;
        $(that).trigger('data:loaded');
    });
};

/**
 * LoadableObject - common parent for service objects
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
 * Restaurants - service
 * @constructor
 */
function Restaurants() {
    this.uri = apiBaseUrl + '/restaurants';

    this.get = function(id) {
        if (this.data[id])
          return this.data[id];
        return undefined;
    };

    this.getIndex = function(restaurant) {
        return this.data.indexOf(restaurant);
    };
}
Restaurants.prototype = new LoadableObject();

/**
 * Menu - service
 * @param restaurant
 * @constructor
 */
function Menu(restaurant) {

    this.uri = apiBaseUrl + '/restaurants/' + restaurant + '/menu';
    this.uriDelete = this.uri + '/delete';

    // postprocess loaded data
    $(this).on('data:loaded', function() {
        this.cached = new Date(this.data.data.attributes.cached);
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

    this.current = undefined;
    this.restaurants = restaurants;
    this.element = 'select#restaurant';

    this.init = function () {
        $(this.restaurants).on('data:loaded', this.view());
        $(this.element).change(this.change());
        $(window).bind('hashchange', this.hashChange());
        this.restaurants.load();
        //(this.hashChange())();
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
            that.current = that.restaurants.get(this.value);
            new MenuView(that.current);
        }
    };

    this.hashChange = function () {
        var that = this;
        return function(event) {
            restaurant = window.location.hash.substr(1);
            if (restaurant != that.current) {
                $(that.element).val(that.restaurants.getIndex(restaurant));
                $(that.element).trigger('change');
            }
        };
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
            var h2 = document.createElement('h2');
            h2.innerHTML = that.restaurant;
            var div = document.createElement('div');
            $(that.element)
                .empty()
                .append(h2)
                .append(
                    $(div)
                        .addClass('item')
                        .append(table)
                        .append('<footer>Načteno ' + that.menu.cached.toLocaleString('cs-CZ', {'timeZone': 'Europe/Prague'}) + '</footer>')
                );
            window.location.hash = that.restaurant;
            window.document.title = that.restaurant + ' - Obědář';
        }
    };

    this.undefined = function() {
        $(this.element)
            .empty().append('<p>Není zvolena žádná restaurace. Prosím, zvolte nějakou z menu.</p>');
    };

    this.init();
}

var restaurantsView = new RestaurantsView(new Restaurants());

$(document).ready(function() {
        restaurantsView.init();
    }
);