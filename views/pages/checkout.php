<div class="main-container">
    <section>
        <div class="form-container">
            <div class="welcome">
                <img src="/img/logo.svg" alt="Logo">
                <h1>Checkout</h1>
            </div>
            <form class="checkout-form" action="/checkoutSubmit" method="post">
                <div class="divided-group">
                    <div class="form-group">
                        <label for="firstname">First Name</label>
                        <input type="text" id="firstname" name="firstname" placeholder="First Name">
                    </div>

                    <div class="form-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" id="lastname" name="lastname" placeholder="Last Name">
                    </div>
                </div>

                <div class="divided-group">
                    <div class="form-group">
                        <label for="country-code">Country Code</label>
                        <select id="country-code" name="country-code">
                            <option value="+33">France (+33)</option>
                            <option value="+1">USA (+1)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="Phone Number" pattern="\d{9,10}"
                               title="Phone number without spaces or country code.">
                    </div>
                </div>

                <div class="form-group">
                    <label for="card-number">Card number</label>
                    <input type="text" id="card-number" name="card-number"
                           placeholder="1234 5678 9012 3456" required>
                </div>

                <div class="divided-group">
                    <div class="divided-group">
                        <div class="form-group">
                            <label for="cardExpiryMonth">Month:</label>
                            <select id="cardExpiryMonth" name="cardExpiryMonth" required>
                                <option value="">Month</option>
                                <?php
                                for ($i = 1; $i <= 12; $i++) {
                                    echo "<option value='$i'>$i</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cardExpiryYear">Year:</label>
                            <select id="cardExpiryYear" name="cardExpiryYear" required>
                                <option value="">Year</option>
                                <?php
                                $currentYear = (int)date('Y');
                                $lastYear = $currentYear + 10;
                                for ($i = $currentYear; $i <= $lastYear; $i++) {
                                    echo "<option value='$i'>$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cardCVC">CVC :</label>
                        <input type="text" id="cardCVC" name="cardCVC" pattern="\d{3}" required>
                    </div>
                </div>

                <div class="divided-group">
                    <div class="form-group">
                        <label for="street-number">Street Number</label>
                        <input type="text" id="street-number" name="street-number" placeholder="Street Number" required>
                    </div>

                    <div class="form-group">
                        <label for="street">Street</label>
                        <input type="text" id="street" name="street" placeholder="Street" required>
                    </div>
                </div>
                <div class="divided-group">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" placeholder="City" required>
                    </div>

                    <div class="form-group">
                        <label for="postal-code">Postal Code</label>
                        <input type="text" id="postal-code" name="postal-code" placeholder="Postal Code" required>
                    </div>
                </div>

                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longitude" name="longitude">

                <div class="submit-group">
                    <button id="login-btn" class="btn-primary" type="submit">Pay</button>
                    <a class="btn-error cancel-btn" href="/cart">Cancel</a>
                </div>
            </form>
        </div>
    </section>
</div>
