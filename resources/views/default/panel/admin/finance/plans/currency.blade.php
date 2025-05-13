@if(isset($subscription))
<option value="{{$subscription->currency}}">{{$subscription->currency}}</option>
@endif
@foreach(\App\Models\Currency::all() as $currency)
    <option value="{{$currency->id}}"{{$setting->default_currency == $currency->id ? 'selected' : ''}}>{{$currency->country.'-'.$currency->code.'-'.$currency->symbol}}</option>
@endforeach
