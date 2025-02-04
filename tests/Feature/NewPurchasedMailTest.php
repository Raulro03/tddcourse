<?php

use App\Mail\NewPurchasedMail;

it('includes purchase details', function () {
    // Arrange
    $course = \App\Models\Course::factory()->create();

    // Act
    $mail = new NewPurchasedMail($course);

    // Assert
    $mail->AssertSeeInText("Thanks for purchasing {$course->title}");
    $mail->AssertSeeInText('Login');
    $mail->AssertSeeInHtml(route('login'));

});
