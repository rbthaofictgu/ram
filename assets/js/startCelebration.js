async function startCelebration() {
    const emojis = ['🎉', '✨', '💫', '🎊', '🔥']; // Emojis de celebración
    const totalParticles = 30; // Más emojis para mayor impacto
    const sound = document.getElementById("celebrationSound");

    if (!sound) {
        console.error("❌ No se encontró el elemento de audio.");
        return;
    }

    console.log("🎵 Intentando reproducir sonido...");

    // Ensure the audio is loaded before playing
    sound.load();
    sound.oncanplaythrough = () => {
        console.log("✅ Audio cargado completamente");
        // Ensure AudioContext is not suspended (needed for Safari & Chrome)
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        console.log("✅ Sonido reproducido correctamente Afuera Audito Conetext " + audioContext);
        audioContext.resume().then(() => {
            console.log("✅ Sonido reproducido correctamente Audito Conetext " + audioContext);
            sound.currentTime = 0;
            sound.volume = 1.0;
            sound.muted = false;
            sound.play().then(() => {
                console.log("✅ Sonido reproducido correctamente");
            }).catch(error => console.error("❌ Error al reproducir sonido:", error));
        }).catch(error => console.error("❌ Error al reanudar AudioContext:", error));
    };

    for (let i = 0; i < totalParticles; i++) {
        const particle = document.createElement('div');
        particle.classList.add('particle');
        particle.innerText = emojis[Math.floor(Math.random() * emojis.length)];
        document.body.appendChild(particle);

        // Posición inicial en la parte superior de la pantalla
        const x = Math.random() * window.innerWidth; // Se distribuyen horizontalmente en la pantalla
        const y = window.innerHeight * 0.2; // Empiezan más arriba

        particle.style.left = `${x}px`;
        particle.style.top = `${y}px`;

        // Animación con GSAP (Subida)
        gsap.to(particle, {
            x: x + (Math.random() - 0.5) * 200, // Movimiento aleatorio horizontal
            y: y + Math.random() * 300, // Suben ligeramente antes de caer
            rotation: Math.random() * 360, // Rotación aleatoria
            scale: Math.random() * 1.2 + 0.8, // Tamaños variados para realismo
            duration: Math.random() * 2 + 2, // Tiempo de animación (más lento)
            ease: "power1.out",
            onComplete: () => {
                // Animación de Caída
                gsap.to(particle, {
                    y: window.innerHeight + 50, // Caen hasta salir de la pantalla
                    opacity: 0, // Desaparecen gradualmente
                    duration: Math.random() * 2 + 2, // La caída dura entre 2 y 4 segundos
                    ease: "power2.in",
                    onComplete: () => particle.remove() // Elimina después de la animación
                });
            }
        });
    }
}