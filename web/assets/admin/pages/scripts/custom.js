/**
Custom module for you to write your own javascript functions
**/
var Custom = function () {

    // public functions
    return {

        //main function
        init: function () {
            //initialize here something.            
        }

    };

}();

/***
Usage
***/
//Custom.init();
//Custom.doSomeStuff();

var App = function() {

    function init()
    {

    }

    return {
        init: init
    }
}();

App.init();