<script type="text/javascript" src="{{ URL::asset('js/classie.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/modalEffects.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/script.js') }}"></script>

<script type="text/javascript">
    // Multiple swithces
    var elem = Array.prototype.slice.call(document.querySelectorAll('.js-primary'));
    elem.forEach(function(checkbox) {
        var switchery = new Switchery(checkbox, { color: '#4099ff', jackColor: '#fff' });
    });
</script>