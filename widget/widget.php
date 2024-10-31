<div class="c-reviewdodo">
    <div class="c-popup">
        <div class="c-popup__close" data-close></div>
        <div class="c-popup__header" style="background: #<?=$reviewContent->base_color?>">
            <div class="c-popup__image">
              <img src="<?= $reviewContent->image_url ?>" height="100" width="100" />
            </div>
        </div>

        <br />

        <form id="reviewdodo-form" method="post" action="<?= $postUrl ?>">
            <div class="c-popup__prefilled">
                <?php foreach ($reviewContent->questions as $question) : ?>
                    <?php if ($question->answer): ?>
                        <h6>
                          <?= $question->question ?>
                          <span>
                            <?php for($i=0;$i<5;$i++):
                              if ($i < $question->answer->score): ?>
                                <i class="fa fa-star"></i>
                              <?php else: ?>
                                <i class="fa fa-star-o"></i>
                              <?php endif;
                            endfor; ?>
                          </span>
                        </h6>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="c-popup__fillable">
              <?php foreach ($reviewContent->questions as $question) : ?>
                  <?php if (!$question->answer): ?>
                      <h5><?= $question->question ?></h5>
                      <div class="c-popup__rating">
                          <input id="question[<?= $question->hash ?>]5" type="radio" value="5" name="question[<?= $question->hash ?>]" class="c-popup__rating-input"/>
                          <label for="question[<?= $question->hash ?>]5" class="c-popup__rating-label"></label>
                          <input id="question[<?= $question->hash ?>]4" type="radio" value="4" name="question[<?= $question->hash ?>]" class="c-popup__rating-input"/>
                          <label for="question[<?= $question->hash ?>]4" class="c-popup__rating-label"></label>
                          <input id="question[<?= $question->hash ?>]3" type="radio" value="3" name="question[<?= $question->hash ?>]" class="c-popup__rating-input"/>
                          <label for="question[<?= $question->hash ?>]3" class="c-popup__rating-label"></label>
                          <input id="question[<?= $question->hash ?>]2" type="radio" value="2" name="question[<?= $question->hash ?>]" class="c-popup__rating-input"/>
                          <label for="question[<?= $question->hash ?>]2" class="c-popup__rating-label"></label>
                          <input id="question[<?= $question->hash ?>]1" type="radio" value="1" name="question[<?= $question->hash ?>]" class="c-popup__rating-input"/>
                          <label for="question[<?= $question->hash ?>]1" class="c-popup__rating-label"></label>
                      </div>
                  <?php endif; ?>
              <?php endforeach; ?>

              <div class="c-popup__hidden">
                <h5>Uw ervaring in één zin</h5>
                <input class="c-popup__input" name="single_line" value="<?= $reviewContent->customer_single_line ?>" type="text" placeholder="Een fijne website met een mooi productaanbod!" />

                <h5>Uw ervaring in het kort</h5>
                <textarea name="free_text" rows="2" class="c-popup__textarea"><?= $reviewContent->customer_text ?></textarea>
              </div>
              <div class="c-popup__submit">
                <input type="hidden" id="reviewdodo-review-hash" value="<?= $reviewContent->hash ?>" name="review_hash" />
                <input type="submit" style="background: #<?=$reviewContent->base_color?>" value="Sla de review op" class="btn">
              </div>
            </div>
        </form>

        <div class="c-reviewdodo__success" id="reviewdodo-success">
            <h2>Bedankt voor uw review</h2>
            <p>We sturen u nu terug naar onze website. Deze pop-up sluit automatisch over <span class="c-reviewdodo__counter">5</span> seconden</p>
            <button class="btn" style="background: #<?=$reviewContent->base_color?>" data-close>Sluiten</button>
        </div>
    </div>
</div>
