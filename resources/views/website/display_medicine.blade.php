@if (count($medicines) > 0)
@if (isset($medicines['data']))
@php
$data = $medicines['data'];
@endphp
@else
@php
$data = $medicines;
@endphp
@endif
@foreach ($data as $value)
<div class="bg-white-50 displayMedicine p-5 border border-white-light hover:drop-shadow-lg hover:border-none">
    <a href="{{ url('medicine-details/'.$value['id'].'/'.Str::slug($value['name'])) }}">
        <img class="lg:h-52 lg:w-full" src="{{ $value['fullImage'] }}" alt="" />
    </a>
    <p class="text-slate-500 text-left font-medium text-lg text-black py-2 leading-5 font-fira-sans">{{ $value['name']
        }}</p>
    <div class="flex justify-between">
        <p class="font-fira-sans font-medium text-xl leading-6 text-primary text-left">{{ $currency }}{{
            $value['price_pr_strip'] }}</p>
        <div class="sessionCart{{$value['id']}}">
            @if (Session::get('cart') == null)
            <a href="javascript:void(0);" onclick="addCart({{$value['id']}},'plus')"
                class="cart text-primary cursor-pointer"><i class="fa-solid fa-bag-shopping"></i></a>
            @else
            @if (in_array($value['id'], array_column(Session::get('cart'), 'id')))
            @foreach (Session::get('cart') as $cart)
            @if($cart['id'] == $value['id'])
            <div class="flex flex-row h-10 w-full rounded-lg relative bg-transparent mt-1">
                <button  data-te-ripple-init data-te-ripple-color="light" id="minus{{ $value['id'] }}" onclick="addCart({{$value['id']}},`minus`)"
                    data-mdb-ripple-color="light" data-action="decrement"
                    class="border-l border-t border-b border-white-light text-black-600 hover:text-black-700 h-8 w-6 cursor-pointer">
                    <span class="m-auto text-2xl font-thin">−</span>
                </button>
                <input type="number" id="txtCart{{ $value['id'] }}" readonly
                    class="border-t border-b border-white-light outline-none focus:outline-none text-center w-10 font-semibold text-md hover:text-black focus:text-black md:text-basecursor-default flex items-center text-primary h-8"
                    name="custom-input-number" value="{{ $cart['qty'] }}">
                <button  data-te-ripple-init data-te-ripple-color="light" onclick="addCart({{$value['id']}},`plus`)" data-mdb-ripple-color="light"
                    data-action="increment"
                    class="border-r border-t border-b border-white-light text-black-600 hover:text-black-700 h-8 w-6 cursor-pointer">
                    <span class="m-auto text-2xl font-thin">+</span>
                </button>
            </div>
            @endif
            @endforeach
            @else
            <a href="javascript:void(0);" onclick="addCart({{$value['id']}},'plus')"
                class="cart text-primary cursor-pointer"><i class="fa-solid fa-bag-shopping"></i></a>
            @endif
            @endif
        </div>
    </div>
</div>
@endforeach
@endif
