$(window).load(function () {
    if (confirm('Do you want to highlight your search token(s)?')) {
        var text = '{{ custom_search }}', el = document.body;
        el.innerHTML = el.innerHTML.replace(
                new RegExp(text + '(?!([^<]+)?>)', 'gi'),
        '<b style="background-color:#ff0;font-size:100%">$&</b>');
    }
});