async function startCelebration() {
    const emojis = ['🎉', '✨', '💫', '🎊', '🔥']; // Emojis de celebración
    const totalParticles = 30; // Más emojis para mayor impacto
    for (let i = 0; i < totalParticles; i++) {
        const particle = document.createElement('div');
        particle.classList.add('particle');
        particle.innerText = emojis[Math.floor(Math.random() * emojis.length)];
        document.body.appendChild(particle);
        //* Posición inicial en la parte superior de la pantalla
        const x = Math.random() * window.innerWidth; // Se distribuyen horizontalmente en la pantalla
        const y = window.innerHeight * 0.2; // Empiezan más arriba
        particle.style.left = `${x}px`;
        particle.style.top = `${y}px`;
        //* Animación con GSAP (Subida)
        gsap.to(particle, {
            x: x + (Math.random() - 0.5) * 200, // Movimiento aleatorio horizontal
            y: y + Math.random() * 300, // Suben ligeramente antes de caer
            rotation: Math.random() * 360, // Rotación aleatoria
            scale: Math.random() * 0.4 + 0.2, // Tamaños variados para realismo
            duration: Math.random() * 1.5 + 1.5, // Tiempo de animación (más lento)
            ease: "power1.out",
            onComplete: () => {
                //* Animación de Caída
                gsap.to(particle, {
                    y: window.innerHeight + 99, // Caen hasta salir de la pantalla
                    opacity: 0, // Desaparecen gradualmente
                    duration: Math.random() * 1.3 + 1.3, // La caída dura entre 2 y 4 segundos
                    ease: "power2.in",
                    onComplete: () => particle.remove() // Elimina después de la animación
                });
            }
        });
    }
}