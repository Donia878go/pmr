<?php
if (!empty($site['audio_is_active'])): 
    $audio_title = !empty($site['audio_title']) ? $site['audio_title'] : 'Mars Politeknik Mulia Rokan';
    $audio_url = !empty($site['audio_url']) ? $site['audio_url'] : 'https://cdn.pixabay.com/download/audio/2022/05/27/audio_1808fbf07a.mp3?filename=inspiring-cinematic-ambient-116199.mp3';
?>
<!-- FLOATING CAMPUS MUSIC PLAYER WIDGET -->
<div id="audio-player-widget" class="fixed bottom-6 left-6 z-40 transition-all duration-300">
  <div class="bg-slate-900/95 backdrop-blur-md text-white p-3 rounded-2xl shadow-2xl border border-teal-500/40 flex items-center gap-3 max-w-xs">
    
    <!-- Audio Element -->
    <audio id="campus-audio" preload="none">
      <source src="<?= htmlspecialchars($audio_url); ?>" type="audio/mpeg">
      <source src="<?= htmlspecialchars($audio_url); ?>" type="audio/ogg">
    </audio>

    <!-- Play/Pause Button -->
    <button id="btn-toggle-audio" type="button" class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-400 text-slate-950 flex items-center justify-center font-black shadow-lg hover:scale-105 active:scale-95 transition-transform shrink-0" title="Putar Mars Kampus">
      <span id="audio-play-icon">▶</span>
    </button>

    <!-- Track Info & Equalizer -->
    <div class="overflow-hidden pr-2">
      <div class="flex items-center gap-1.5 mb-0.5">
        <span class="text-[9px] uppercase font-black text-amber-400 tracking-wider">Audio Kampus</span>
        <div id="audio-equalizer" class="flex items-end gap-0.5 h-4 paused">
          <span class="equalizer-bar"></span>
          <span class="equalizer-bar"></span>
          <span class="equalizer-bar"></span>
          <span class="equalizer-bar"></span>
        </div>
      </div>
      <p class="text-xs font-bold text-slate-200 truncate" title="<?= htmlspecialchars($audio_title); ?>">
        <?= htmlspecialchars($audio_title); ?>
      </p>
    </div>

    <!-- Close / Hide Button -->
    <button type="button" onclick="document.getElementById('audio-player-widget').style.display='none'" class="text-slate-500 hover:text-slate-300 text-xs p-1" title="Sembunyikan">
      &times;
    </button>

  </div>
</div>

<script>
(function() {
  var audio = document.getElementById('campus-audio');
  var btn = document.getElementById('btn-toggle-audio');
  var icon = document.getElementById('audio-play-icon');
  var eq = document.getElementById('audio-equalizer');

  if (btn && audio) {
    btn.addEventListener('click', function() {
      if (audio.paused) {
        audio.play().then(function() {
          icon.innerHTML = '⏸';
          eq.classList.remove('paused');
        }).catch(function(e) {
          console.log('Audio autoplay blocked or failed:', e);
        });
      } else {
        audio.pause();
        icon.innerHTML = '▶';
        eq.classList.add('paused');
      }
    });

    audio.addEventListener('ended', function() {
      icon.innerHTML = '▶';
      eq.classList.add('paused');
    });
  }
})();
</script>
<?php endif; ?>
