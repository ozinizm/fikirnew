"use client";

import { useEffect, useState } from "react";
import { motion, useMotionValue, useSpring } from "framer-motion";

export default function CustomCursor() {
  const [mounted, setMounted] = useState(false);
  const [hovered, setHovered] = useState(false);
  const [cursorText, setCursorText] = useState("");
  const [visible, setVisible] = useState(false);

  const cursorX = useMotionValue(-100);
  const cursorY = useMotionValue(-100);

  const springConfig = { damping: 40, stiffness: 350, mass: 0.5 };
  const cursorXSpring = useSpring(cursorX, springConfig);
  const cursorYSpring = useSpring(cursorY, springConfig);

  useEffect(() => {
    // Only enable cursor on devices with fine pointer (mouse)
    const mediaQuery = window.matchMedia("(pointer: fine)");
    if (!mediaQuery.matches) return;

    setMounted(true);

    const moveCursor = (e: MouseEvent) => {
      cursorX.set(e.clientX);
      cursorY.set(e.clientY);
      setVisible(true);
    };

    const handleMouseLeave = () => setVisible(false);
    const handleMouseEnter = () => setVisible(true);

    window.addEventListener("mousemove", moveCursor);
    document.addEventListener("mouseleave", handleMouseLeave);
    document.addEventListener("mouseenter", handleMouseEnter);

    const handleMouseOver = (e: MouseEvent) => {
      const target = e.target as HTMLElement;
      if (!target) return;

      const interactive = 
        target.tagName === "A" ||
        target.tagName === "BUTTON" ||
        target.closest("a") ||
        target.closest("button") ||
        target.closest('[role="button"]') ||
        target.classList.contains("clickable") ||
        target.closest(".clickable") ||
        target.classList.contains("cursor-pointer") ||
        target.closest(".cursor-pointer");

      if (interactive) {
        setHovered(true);
        const closestInteractive = target.closest("[data-cursor-text]");
        if (closestInteractive) {
          setCursorText(closestInteractive.getAttribute("data-cursor-text") || "");
        } else {
          setCursorText("");
        }
      } else {
        setHovered(false);
        setCursorText("");
      }
    };

    window.addEventListener("mouseover", handleMouseOver);

    return () => {
      window.removeEventListener("mousemove", moveCursor);
      document.removeEventListener("mouseleave", handleMouseLeave);
      document.removeEventListener("mouseenter", handleMouseEnter);
      window.removeEventListener("mouseover", handleMouseOver);
    };
  }, [cursorX, cursorY]);

  // Manage body class dynamically based on visibility and mount status
  useEffect(() => {
    if (mounted && visible) {
      document.body.classList.add("custom-cursor-active");
    } else {
      document.body.classList.remove("custom-cursor-active");
    }
    return () => {
      document.body.classList.remove("custom-cursor-active");
    };
  }, [mounted, visible]);

  if (!mounted || !visible) return null;

  return (
    <>
      {/* Outer Ring with spring lag */}
      <motion.div
        className="fixed top-0 left-0 rounded-full border border-white/35 pointer-events-none z-[9999] flex items-center justify-center"
        style={{
          x: cursorXSpring,
          y: cursorYSpring,
          translateX: "-50%",
          translateY: "-50%",
          width: hovered ? (cursorText ? 85 : 56) : 28,
          height: hovered ? (cursorText ? 85 : 56) : 28,
          backgroundColor: hovered ? "rgba(255, 255, 255, 0.08)" : "rgba(255, 255, 255, 0)",
          backdropFilter: hovered ? "blur(1px)" : "blur(0px)",
          mixBlendMode: "difference",
        }}
        animate={{
          scale: hovered ? 1.15 : 1,
        }}
        transition={{ type: "spring", stiffness: 350, damping: 28 }}
      >
        {cursorText && (
          <span className="text-[10px] text-white font-bold uppercase tracking-widest text-center select-none pointer-events-none px-2 scale-[0.85] leading-none">
            {cursorText}
          </span>
        )}
      </motion.div>

      {/* Inner Dot with direct movement */}
      <motion.div
        className="fixed top-0 left-0 w-2 h-2 bg-white rounded-full pointer-events-none z-[10000]"
        style={{
          x: cursorX,
          y: cursorY,
          translateX: "-50%",
          translateY: "-50%",
          mixBlendMode: "difference",
        }}
        animate={{
          scale: hovered ? 0 : 1,
        }}
        transition={{ duration: 0.15 }}
      />
    </>
  );
}
