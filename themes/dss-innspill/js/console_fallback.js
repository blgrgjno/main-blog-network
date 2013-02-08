(function (fallback) {    

    fallback = fallback || function () { };

    // function to trap most of the console functions from the FireBug Console API
    var trap = function () {
        var args = Array.prototype.slice.call(arguments);        
        var message = args.join(' ');
        console.messages.push(message);
        fallback(message);
    };

    // redefine console
    if (typeof console === 'undefined') {
        console = {
            messages: [],
            dump: function() { return console.messages.join('\n'); },
            log: trap,
            debug: trap,
            info: trap,
            warn: trap,
            error: trap,
            assert: trap,
            clear: function() { console.messages.length = 0 },
            dir: trap,
            dirxml: trap,
            trace: trap,
            group: trap,
            groupCollapsed: trap,
            groupEnd: trap,
            time: trap,
            timeEnd: trap,
            timeStamp: trap,
            profile: trap,
            profileEnd: trap,
            count: trap,
            exception: trap,
            table: trap
        };
    }

})(null); // to define a fallback function, replace null with the name of the function (ex: alert)