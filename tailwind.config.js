import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                "sacli-green": {
                    50: "#D1FAE5", // Light green
                    100: "#A7F3D0",
                    200: "#6EE7B7",
                    300: "#34D399", // Accent
                    400: "#10B981", // Primary green
                    500: "#059669", // Secondary green
                    600: "#047857", // Dark green
                    700: "#065F46",
                    800: "#064E3B",
                    900: "#022C22",
                },
            },
            aspectRatio: {
                "4/3": "4 / 3",
                "16/9": "16 / 9",
            },
        },
    },

    plugins: [
        forms,
        function ({ addUtilities }) {
            addUtilities({
                ".line-clamp-1": {
                    display: "-webkit-box",
                    "-webkit-line-clamp": "1",
                    "-webkit-box-orient": "vertical",
                    overflow: "hidden",
                },
                ".line-clamp-2": {
                    display: "-webkit-box",
                    "-webkit-line-clamp": "2",
                    "-webkit-box-orient": "vertical",
                    overflow: "hidden",
                },
                ".line-clamp-3": {
                    display: "-webkit-box",
                    "-webkit-line-clamp": "3",
                    "-webkit-box-orient": "vertical",
                    overflow: "hidden",
                },
            });
        },
    ],
};
