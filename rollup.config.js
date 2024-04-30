import { terser } from 'rollup-plugin-terser';
import outputManifest from 'rollup-plugin-output-manifest';

export default {
    input: `assets/easy-popup.js`,
    output: {
        dir: 'public/',
        entryFileNames: '[name].[hash].min.js',
        format: 'iife',
    },
    plugins: [terser(), outputManifest()],
};
