function date(format, timestamp) {
    var that = this;
    var jsdate, f;
    var txt_words = ["Sun", "Mon", "Tues", "Wednes", "Thurs", "Fri", "Satur", "January", "February", "March", "April",
            "May", "June", "July", "August", "September", "October", "November", "December"];
    var formatChr = /\\?(.?)/gi;
    var formatChrCb = function (t, s) {
        return f[t] ? f[t]() : s
    };
    var _pad = function (n, c) {
        n = String(n);
        while (n.length < c) {
            n = "0" + n
        }
        return n
    };
    f = {
        d: function () {
            return _pad(f.j(), 2)
        },
        D: function () {
            return f.l().slice(0, 3)
        },
        j: function () {
            return jsdate.getDate()
        },
        l: function () {
            return txt_words[f.w()] + "day"
        },
        N: function () {
            return f.w() || 7
        },
        S: function () {
            var j = f.j();
            var i = j % 10;
            if (i <= 3 && parseInt((j % 100) / 10, 10) == 1) {
                i = 0
            }
            return ["st", "nd", "rd"][i - 1] || "th"
        },
        w: function () {
            return jsdate.getDay()
        },
        z: function () {
            var a = new Date(f.Y(), f.n() - 1, f.j());
            var b = new Date(f.Y(), 0, 1);
            return Math.round((a - b) / 86400000)
        },
        W: function () {
            var a = new Date(f.Y(), f.n() - 1, f.j() - f.N() + 3);
            var b = new Date(a.getFullYear(), 0, 4);
            return _pad(1 + Math.round((a - b) / 86400000 / 7), 2)
        },
        F: function () {
            return txt_words[6 + f.n()]
        },
        m: function () {
            return _pad(f.n(), 2)
        },
        M: function () {
            return f.F().slice(0, 3)
        },
        n: function () {
            return jsdate.getMonth() + 1
        },
        t: function () {
            return (new Date(f.Y(), f.n(), 0)).getDate()
        },
        L: function () {
            var j = f.Y();
            return j % 4 === 0 & j % 100 !== 0 | j % 400 === 0
        },
        o: function () {
            var n = f.n();
            var W = f.W();
            var Y = f.Y();
            return Y + (n === 12 && W < 9 ? 1 : n === 1 && W > 9 ? -1 : 0)
        },
        Y: function () {
            return jsdate.getFullYear()
        },
        y: function () {
            return f.Y().toString().slice(-2)
        },
        a: function () {
            return jsdate.getHours() > 11 ? "pm" : "am"
        },
        A: function () {
            return f.a().toUpperCase()
        },
        B: function () {
            var H = jsdate.getUTCHours() * 3600;
            var i = jsdate.getUTCMinutes() * 60;
            var s = jsdate.getUTCSeconds();
            return _pad(Math.floor((H + i + s + 3600) / 86.4) % 1000, 3)
        },
        g: function () {
            return f.G() % 12 || 12
        },
        G: function () {
            return jsdate.getHours()
        },
        h: function () {
            return _pad(f.g(), 2)
        },
        H: function () {
            return _pad(f.G(), 2)
        },
        i: function () {
            return _pad(jsdate.getMinutes(), 2)
        },
        s: function () {
            return _pad(jsdate.getSeconds(), 2)
        },
        u: function () {
            return _pad(jsdate.getMilliseconds() * 1000, 6)
        },
        e: function () {
            throw "Not supported (see source code of date() for timezone on how to add support)"
        },
        I: function () {
            var a = new Date(f.Y(), 0);
            var c = Date.UTC(f.Y(), 0);
            var b = new Date(f.Y(), 6);
            var d = Date.UTC(f.Y(), 6);
            return ((a - c) !== (b - d)) ? 1 : 0
        },
        O: function () {
            var tzo = jsdate.getTimezoneOffset();
            var a = Math.abs(tzo);
            return (tzo > 0 ? "-" : "+") + _pad(Math.floor(a / 60) * 100 + a % 60, 4)
        },
        P: function () {
            var O = f.O();
            return (O.substr(0, 3) + ":" + O.substr(3, 2))
        },
        T: function () {
            return "UTC"
        },
        Z: function () {
            return -jsdate.getTimezoneOffset() * 60
        },
        c: function () {
            return "Y-m-d\\TH:i:sP".replace(formatChr, formatChrCb)
        },
        r: function () {
            return "D, d M Y H:i:s O".replace(formatChr, formatChrCb)
        },
        U: function () {
            return jsdate / 1000 | 0
        }
    };
    this.date = function (format, timestamp) {
        that = this;
        jsdate = (timestamp === undefined ? new Date() : (timestamp instanceof Date) ? new Date(timestamp) : new Date(
            timestamp * 1000));
        return format.replace(formatChr, formatChrCb)
    };
    return this.date(format, timestamp)
};
function empty(mixed_var) {
    var undef, key, i, len;
    var emptyValues = [undef, null, false, 0, "", "0"];
    for (i = 0, len = emptyValues.length; i < len; i++) {
        if (mixed_var === emptyValues[i]) {
            return true
        }
    }
    if (typeof mixed_var === "object") {
        for (key in mixed_var) {
            return false
        }
        return true
    }
    return false
};
var is_phone = function (num) {
    var reg = /^1\d{10}$/;
    return reg.test(num)
};