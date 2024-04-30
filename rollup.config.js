import { terser } from 'rollup-plugin-terser';

export default {
    input: `assets/easy-popup.js`,
    output: {
        file: `public/easy-popup.min.js`,
        format: 'iife',
    },
    plugins: [terser()],
};
