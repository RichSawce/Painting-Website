# Painting-Website
My first launched website for an abstract painter
A lightweight, fast, and accessible portfolio for Canadian acrylic painter Francine Carignan. The site showcases available works with sizes/prices, series collections, exhibition history, and a simple contact form.

Live site: paintingsbyfrancine.ca
Location: Victoria, British Columbia, Canada. Exhibitions across Canada.  ￼
Instagram: @francines_paintings  ￼

⸻

Features
	•	Gallery of current works with titles, sizes, and prices (e.g., Joyful, Flower Power, Random Thoughts, Ascension Of Man (SOLD!), Telephone Poles, etc.).  ￼
	•	Series sections (e.g., NEST SERIES, FOREST SERIES, FIRE WITHIN SERIES).  ￼
	•	Exhibitions section (Shine Cafe Gallery – May 2022; Mile Zero Gallery – multiple runs; Fairfield Art Walk – 2024).  ￼
	•	Contact page with a simple inquiry form (first/last name, email, message).  ￼

The site is simple HTML/CSS/JS (no framework required), designed to be easily hosted on standard cPanel hosting.

⸻

Tech Stack
	•	Frontend: HTML5, CSS3, vanilla JavaScript
	•	Hosting: Any static-friendly host (cPanel/Apache works great)
	•	Email/Contact: Simple form (server-side handler recommended – see “Contact Form” below)
  
  paintingsbyfrancine/
├─ index.html              # Landing page (selected highlights)
├─ gallery.html            # Full gallery with prices/sizes/series
├─ contactus.html          # Contact form page
├─ /assets
│  ├─ /images              # Artwork images (JPG/PNG/WEBP)
├─ /css
│  └─ styles.css           # Site styles
├─ /js
│  └─ main.js              # Popup, gallery, and minor UI logic
├─ /server (optional)
│  └─ contact.php          # Mail handler if using PHP/cPanel
└─ README.md

