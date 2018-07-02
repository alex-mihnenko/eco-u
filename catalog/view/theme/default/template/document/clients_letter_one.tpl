<style type="text/css">
  .coupon {
    display: flex;
    justify-content: center;
    align-items: center;
    align-content: center;
    flex-direction: row;

    width: 100%;
    height: auto;
    border: 1px solid #eee;
    background-color: #eee;
    border-radius: 2px;
    box-sizing: border-box;
    padding: 30px;
  }

  .coupon > .discount {
    width: 50%;
    text-align: center;
  }

  .coupon > .discount label {
    font-size: 42px;
    font-weight: bold;
    color: red;
    text-transform: uppercase;
  }

  .coupon > .about {
    width: 50%;
    text-align: center;
  }

  .coupon > .about label {
    font-size: 34px;
    font-weight: bold;
    text-transform: uppercase;
  }
</style>



<main>
  <h2 style="text-align: center;">Здравствуйте <?php echo $customer_name; ?>!</h2>

  <p>
    Меня зовут Александр.  Спасибо, что сделали заказ в Eco-U. Надеюсь, что вам понравятся продукты.
    <br><br>
    
    Я написал это письмо, чтобы попросить помощи. У вас это займёт всего пару минут, но вы поможете покупателям Eco-U получать качественные продукты и обслуживание.
    <br><br>

    В конце письма указан телефон. Пожалуйста, запишите мне звуковое сообщение в WhatsApp, Viber или Telegram (или просто напишите текстовое сообщение или SMS, если так вам удобнее). Расскажите понравилось ли вам обслуживание, сайт, продукты, доставка и что мы можем улучшить?
    <br><br>

    Я с радостью выслушаю и похвалу и критику. Плохие отзывы помогают нам быстро исправляться, чтобы не огорчать покупателей, а хорошие поднимают настроение всей команде и вдохновляют работать ещё лучше.
    <br><br><br>

    Искренне ваш,<br>
    <b>Александр Михненко</b>
    <br><br>
    
    Директор по развитию ЭКО-Ю
    <br><br>

    <b>P.S.</b><br>
    В этом письме прикладываю промо-код на <?php echo $coupon_discount; ?>% скидку, который действителен 2 недели (до <?php echo $coupon_end; ?>). Обычно наши клиенты покупают продукты раз в неделю, но если вы делаете это реже, можете подарить купон друзьям или близким.
  </p>

  <br><br>

  <div class="coupon">
    
    <div class="discount">
        <label>Скидка <?php echo $coupon_discount; ?>%</label>
    </div>

    <div class="about">
        <label>Промокод</label>
        <br><br>
        <label><?php echo $coupon_code; ?></label>
        <br><br>

        <span>действителен до <?php echo $coupon_end; ?></span>
    </div>

  </div>
</main>





<script type="text/javascript">
  window.onload = function() {
    // ---
      setTimeout(function(){
        window.print();
      }, 1500);
    // ---
  }
</script>