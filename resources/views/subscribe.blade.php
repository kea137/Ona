<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-3 w-1/2">
                        <form action="{{ route('subscribe.post') }}" method="post" id="payment-form" data-secret="{{ $intent->client_secret }}">
                            @csrf
                            <div>
                                <input type="radio" value="price_1J12RMEqqNURwckKGl4WUoy7" id="Standard" name="plan" checked>
                                <label for="Standard">Standard Plan</label><br>
                                <input type="radio" value="price_1J12RMEqqNURwckKir5DwztI" id="Premium" name="plan">
                                <label for="Premium">Premium Plan</label>
                            </div>
                            <div class="form-row">
                            <label for="card-element">
                                Credit or debit card
                            </label>
                            <div class=" flex-col">
                                <input class=" border-transparent shadow-md rounded-sm focus:border-transparent m-3" type="text" name="cardholder-name" id="Cardholder">
                                <label for="Cardholder">Cardholder's Name</label>
                            </div>
                            <div id="card-element" class=" border-b m-3 border-gray-100">
                                <!-- A Stripe Element will be inserted here. -->
                            </div>

                            <!-- Used to display Element errors. -->
                            <div id="card-errors" role="alert"></div>
                            </div>

                            <button class=" bg-indigo-400 hover:bg-indigo-500 p-1 m-2 rounded-md text-white shadow-lg">Submit Payment</button>
                        </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        var stripe = Stripe('pk_test_51Hsg0gEqqNURwckKl748gQk3pRNCOIulbFqLEW07f5cBGjc7YdtxAvpzVqc0cAQ579cSEyamcMx74XYt3BRoR8p900U4ncYf6K');
        var elements = stripe.elements();
        // Custom styling can be passed to options when creating an Element.
        var style = {
        base: {
            // Add your base input styles here. For example:
            fontSize: '16px',
            color: '#32325d',
        },
        };
        // Create an instance of the card Element.
        var card = elements.create('card', {style: style});

        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');

        // Create a token or display an error when the form is submitted.
        var form = document.getElementById('payment-form');
        var cardholder_name = document.getElementById('Cardholder');
        var clientSecret = form.dataset.secret;

        form.addEventListener('submit',async function(event) {
        event.preventDefault();

        const { setupIntent, error } = await stripe.confirmCardSetup(
        clientSecret, {
            payment_method: {
                card,
                billing_details: { name: cardholder_name.value }
                }
            }
        );

        if (error) {
            var errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
        } else {
            console.log(setupIntent);
            stripeTokenHandler(setupIntent);
        }

        // if (error) {
        //     // Inform the customer that there was an error.
        //
        // } else {
        //     // Send the token to your server.
        //     console.log(setupIntent);
        // }

        // stripe.createToken(card).then(function(result) {
        //     if (result.error) {
        //     } else {

        //     }
        // });
        });

        function stripeTokenHandler(setupIntent) {
        // Insert the token ID into the form so it gets submitted to the server
        var form = document.getElementById('payment-form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'stripeToken');
        hiddenInput.setAttribute('value', setupIntent.payment_method);
        form.appendChild(hiddenInput);

        // Submit the form
        form.submit();
        }
    </script>
    @endpush
</x-app-layout>
