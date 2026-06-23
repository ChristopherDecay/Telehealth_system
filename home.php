<?php
include "header.php"; ?>

<main class="home-page">
    <section class="home-split home-reveal home-reveal--1">
        <div class="home-split__text">
            <p class="home-eyebrow">Secure &bull; Connected &bull; Reliable</p>
            <h1>TeleHealth that keeps care teams in sync.</h1>
            <p class="home-lede">
                One platform for patients, caregivers, doctors, nurses and lab technicians to
                coordinate appointments, results, and follow-ups with confidence.
            </p>
            <div class="home-cta">
                <button class="home-btn home-btn--primary" type="button" onclick="location.href='/Telehealth_system/register.php'">Get Started</button>
                <button class="home-btn home-btn--ghost" type="button" onclick="location.href='/Telehealth_system/login.php'">Sign In</button>
            </div>
        </div>
        <div class="home-split__media">
            <img src="/Telehealth_system/img/slideshow_img_4.jpg" alt="Telehealth consultation" />
        </div>
    </section>

    <section class="home-split home-split--reverse home-reveal home-reveal--2">
        <div class="home-split__text">
            <h2>Clinical-ready workflows</h2>
            <p>Structured visits, lab routing, and notes that keep the full care team aligned.</p>
            <p>Clear dashboards and notifications for admins, providers, labs, and caregivers.</p>
        </div>
        <div class="home-split__media">
            <img src="/Telehealth_system/img/slideshow_img_1.jpg" alt="Care team collaboration" />
        </div>
    </section>

    <section class="home-split home-reveal home-reveal--3">
        <div class="home-split__text">
            <h2>Virtual-ready visits</h2>
            <p>Support remote consultations with clear context, follow-up planning, and secure messaging.</p>
            <p>Patients get easy access to appointments, updates, and next steps in one place.</p>
        </div>
        <div class="home-split__media">
            <img src="/Telehealth_system/img/slideshow_img_2.jpg" alt="Virtual visit in progress" />
        </div>
    </section>

    <section class="home-split home-split--reverse home-reveal home-reveal--4">
        <div class="home-split__text">
            <h2>Labs & results, delivered fast</h2>
            <p>Route results directly to the right clinician and patient dashboard.</p>
            
            <div class="home-cta">
                <button class="home-btn home-btn--primary" type="button" onclick="location.href='/Telehealth_system/register.php'">Create Account</button>
            </div>
        </div>
        <div class="home-split__media">
            <img src="/Telehealth_system/img/slideshow_img_3.jpg" alt="Lab results review" />
        </div>
    </section>
</main>

<?php
include "footer.php"; ?>
