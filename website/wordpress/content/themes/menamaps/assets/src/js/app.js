import '../css/app.css';
import '../css/social_media_icons.css';
import '../css/post_engagement.css';
import '../css/comments.css';

// -----------------------------------------------------------------------------

// js detection - class no-js to js

(function(html){

	html.className = html.className.replace(/\bno-js\b/, 'js');

})(document.documentElement);

// -----------------------------------------------------------------------------

import 'htmx.org';

// -----------------------------------------------------------------------------

import Alpine from 'alpinejs';

import sort from '@alpinejs/sort';
Alpine.plugin( sort );

window.Alpine = Alpine;

Alpine.start();

// -----------------------------------------------------------------------------

import './color_scheme';
import './share';
import './external_links';
import './content_image_generator';
