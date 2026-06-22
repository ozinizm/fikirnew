            </div> <!-- /Page Content -->

        </main>
    </div>
<div id="cookie-box" style="display:none;" class="fixed bottom-6 left-6 right-6 md:left-auto md:max-w-sm bg-[#111] text-white p-6 rounded-3xl shadow-2xl z-[9999] border border-white/10">
    <p class="text-xs text-gray-400 mb-4 leading-relaxed">
        Size daha iyi hizmet sunabilmek için çerezleri kullanıyoruz. <a href="cerezler.php" class="text-orange underline">Politikamızı oku.</a>
    </p>
    <button onclick="acceptCookies()" class="w-full bg-[#F15A24] py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-white hover:text-orange transition-all">
        KABUL EDİYORUM
    </button>
</div>

<script>
    // Tarayıcı çerezi kontrol eder, kabul edilmediyse kutuyu gösterir
    if(!localStorage.getItem('fikir_cookies')) {
        document.getElementById('cookie-box').style.display = 'block';
    }

    function acceptCookies() {
        localStorage.setItem('fikir_cookies', 'true');
        document.getElementById('cookie-box').style.opacity = '0';
        setTimeout(() => document.getElementById('cookie-box').style.display = 'none', 500);
    }
</script>
</body>
</html>
