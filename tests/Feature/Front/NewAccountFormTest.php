<?php

namespace Tests\Feature\Front;

use App\Http\Livewire\Front\NewAccountForm;
use App\Mail\ConfirmationRegisterUserDemand;
use App\Mail\RegisterUserDemand;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

test("Access to the new account form", function() {
    get(route('account.new'))
        ->assertStatus(200);
});

test("Send new account registration", function() {
    \Mail::fake();

    livewire(NewAccountForm::class)
        ->set('user.nom', 'test')
        ->set('user.prenom', 'test')
        ->set('user.email', 'test@test.com')
        ->set('user.telephone', '0987654434')
        ->set('user.adresse', '33 rue john doe')
        ->set('user.code_postal', '34000')
        ->set('user.ville', 'Montpellier')
        ->set('entrepriseName', 'Nom de entreprise')
        ->call('send')
        ->assertHasNoErrors();

    \Mail::assertSent(RegisterUserDemand::class, function(RegisterUserDemand $mail) {
        return $mail->hasSubject('MOTOBLEU / Demande de création de compte') &&
            $mail->assertSeeInHtml('Demande de création de compte');
    });

    \Mail::assertSent(ConfirmationRegisterUserDemand::class, function(ConfirmationRegisterUserDemand $mail) {
        return $mail->hasTo('test@test.com') &&
            $mail->hasSubject('MOTOBLEU / Confirmation de la demande de création de compte') &&
            $mail->assertSeeInHtml('Nous avons bien reçu votre message, il sera traité dans les plus brefs délais.');
    });
});

test("Send new account registration with errors", function() {
    livewire(NewAccountForm::class)
        ->call('send')
        ->assertHasErrors([
            'user.nom',
            'user.prenom',
            'user.email',
            'user.telephone',
            'user.adresse',
            'user.code_postal',
            'user.ville',
            'entrepriseName'
        ]);
});
