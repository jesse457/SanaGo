import { Link } from '@inertiajs/react';
import { useId } from 'react';
import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

/**
 * The SanaGo identity.
 *
 * The mark is the hospital's own promise drawn as one figure: a person leaning
 * into a heart that is also a leaf and a medical cross. The blue half is the
 * patient (head, shoulder, the arm that reaches); the green half is the care
 * given (heart, renewal, the clinic). They meet at the point where a heart
 * comes to its tip, so the two halves are one heart rather than two badges
 * placed side by side.
 *
 * It is a two-ink mark, not a single-colour glyph, so it carries its own
 * colours instead of using `currentColor`: those blues and greens are the
 * identity, and they stay the same on paper, on the dark social card and in
 * the app. Everything around the mark (wordmark, captions) uses the semantic
 * `brand-figure-ink` / `brand-care-ink` tokens, which lift to stay legible in
 * dark mode.
 *
 * The path data is a curve-for-curve transcription of the approved artwork, so
 * the React mark and the shipped `public/brand/mark.svg` are the same drawing.
 */

const MARK_VIEWBOX = '0 0 439 392';

const FIGURE_PATH =
    'M105.5 2 L105 2.5 L98 2.5 L97 3.5 L88 4.5 Q87 5.5 83 6.5 Q79 7.5 69 12.5 Q59 17.5 58 18.5 Q57 19.5 55 20.5 Q53 21.5 51 23.5 L49 25.5 L48 25.5 L41 32.5 L40 32.5 L31.5 42 L31.5 43 Q28.5 46 27.5 48 Q26.5 50 25 51.5 Q23.5 53 20.5 59 Q17.5 65 16.5 66 L15.5 67 Q15.5 69 14 71.5 Q12.5 74 12.5 75 Q12.5 76 11.5 77.5 Q10.5 79 10 81.5 Q9.5 84 8.5 85.5 Q7.5 87 7 90.5 Q6.5 94 6 94.5 Q5.5 95 5 98 Q4.5 101 4 101.5 L3.5 102 L3.5 108 L2.5 109 L2.5 115 L1.5 116 L1.5 144 L2.5 145 L2.5 152 L3.5 153 Q4.5 163 5.5 165 Q6.5 167 7 170.5 Q7.5 174 8.5 175.5 Q9.5 177 10.5 181 Q11.5 185 14.5 191 Q17.5 197 18 199 Q18.5 201 19.5 202 Q20.5 203 22.5 207.5 Q24.5 212 26 213.5 Q27.5 215 28 216.5 Q28.5 218 29.5 219 Q30.5 220 31 222 L31.5 224 Q34.5 226 35 227.5 Q35.5 229 37.5 231 L39.5 233 L39.5 234 L43.5 238 L43.5 239 L49.5 245 L49.5 246 L51 246.5 Q51.5 248 58.2 254.8 L65 261.5 L66 261.5 L81 275.5 L82 275.5 L86 279.5 L87 279.5 L90 282.5 L91 282.5 L95 286.5 L96 286.5 Q103 292.5 105 293.5 Q107 294.5 111 298 L115 301.5 L116 301.5 L126 309.5 L127 309.5 Q131 313.5 134.5 315.5 Q138 317.5 139.5 319 L141 320.5 L142 320.5 L148 325.5 L149 325.5 L155 330.5 L156 330.5 L169 342.5 L170 342.5 L171.5 344 L171.5 345 L177.5 350 L177.5 351 L182.5 356 L182.5 357 Q185.5 359 186.5 361 Q187.5 363 190.5 366.5 L193.5 370 Q193.5 371 198.5 378 Q203.5 385 204.8 387.8 L206 390.5 L207 390.5 L208.5 389 L208.5 386 L207.5 385 L207.5 382 L206.5 381 L206.5 379 L205.5 378 Q205.5 376 204.5 374 Q203.5 372 203.5 370.5 Q203.5 369 202 366.5 Q200.5 364 200.5 363 Q200.5 362 198.5 358 Q196.5 354 195.5 353 Q194.5 352 192.5 348 Q190.5 344 186.5 339.5 L182.5 335 L182.5 334 L169 320.5 L168 320.5 L161 313.5 L160 313.5 L155 308.5 L154 308.5 L142.5 298 L142.5 297 Q136.5 291 135 288 Q133.5 285 132 283.5 L130.5 282 L130.5 280 L129.5 279 L130 277.5 L132 279.5 L133 279.5 L142 287.5 L143 287.5 Q154 296.5 155.5 297 Q157 297.5 158.5 299 L160 300.5 L161 300.5 L163 302.5 L164 302.5 L166 304.5 L167 304.5 L169 306.5 L170 306.5 Q172 308.5 175.5 310.5 Q179 312.5 182.5 315.5 L186 318.5 L187 318.5 L203.5 335 L203.5 336 Q206.5 339 207 340.5 Q207.5 342 209.5 344.5 Q211.5 347 214.5 353 Q217.5 359 217.5 360 Q217.5 361 218.5 362.5 Q219.5 364 219.5 365 Q219.5 366 220.5 367.5 Q221.5 369 221.5 370 L221.5 371 L223.5 371 L223.5 360 L222.5 359 L222.5 356 Q221.5 355 221 351.5 Q220.5 348 219 345 Q217.5 342 217.5 341 Q217.5 340 216.5 338.5 Q215.5 337 215.5 336 Q215.5 335 214 332 Q212.5 329 211.5 328 Q210.5 327 210 325.5 Q209.5 324 208.5 323 Q207.5 322 205.5 318.5 Q203.5 315 200.5 311.5 L197.5 308 L197.5 307 L187.5 296 L187.5 295 L145.5 251 L145.5 250 Q137.5 240 135.5 236 Q133.5 232 133.5 230.5 L133.5 229 L132.5 228 L132.5 225 L131.5 224 L131.5 220 L130.5 219 L130.5 212 L129.5 211 L129.5 187 L130.5 186 L130.5 181 L131.5 180 L131.5 176 Q132.5 175 133 171.5 Q133.5 168 134.5 166.5 Q135.5 165 136 163 Q136.5 161 137.5 160 L138.5 159 Q138.5 157 139.5 155 Q140.5 153 141.5 152 Q142.5 151 143.5 149 Q144.5 147 150.8 140.8 Q157 134.5 164 130.5 Q171 126.5 172 126.5 Q173 126.5 174.5 125.5 Q176 124.5 177 124.5 Q178 124.5 179.2 123.8 L180.5 123 Q178 121.5 173.5 121.5 L169 121.5 L168 122.5 L161 122.5 L160 123.5 L156 123.5 L155 124.5 L152 124.5 Q151 125.5 147.5 126 Q144 126.5 141.5 128 Q139 129.5 138 129.5 Q137 129.5 134 131 Q131 132.5 130 133.5 Q129 134.5 127 135.5 Q125 136.5 123.5 138 L122 139.5 L121 139.5 L109.5 150 L109.5 151 L104.5 156 Q104.5 157 101.5 161 Q98.5 165 94 174 Q89.5 183 88.5 187 Q87.5 191 86.5 192.5 Q85.5 194 85.5 195 Q85.5 196 84.8 197.2 L84 198.5 L83 198.5 L69.5 185 L69.5 184 Q67.5 182 64.5 176 Q61.5 170 60.5 169 L59.5 168 Q59.5 166 58.5 164.5 Q57.5 163 57.5 162 L57.5 161 L56.5 160 Q56.5 158 55.5 156 Q54.5 154 54.5 152.5 L54.5 151 L53.5 150 L53.5 147 L52.5 146 L52.5 143 L51.5 142 L51.5 136 L50.5 135 L50.5 125 L49.5 124 L49.5 111 L50.5 110 L50.5 101 L51.5 100 L51.5 94 L52.5 93 L52.5 89 Q53.5 88 54 85 Q54.5 82 56 79 Q57.5 76 58 73.5 Q58.5 71 59.5 69 Q60.5 67 62.5 64.5 Q64.5 62 66 59 Q67.5 56 70 53.5 Q72.5 51 72.8 50.2 L73 49.5 L74 49.5 L84 40.5 L85 40.5 Q90 36.5 91.5 36 Q93 35.5 94 34.5 Q95 33.5 97 32.5 Q99 31.5 100 31.5 Q101 31.5 105 29.5 Q109 27.5 110.5 27.5 L112 27.5 L113 26.5 L116 26.5 L117 25.5 L120 25.5 L121 24.5 L125 24.5 L126 23.5 L134 23.5 L135 22.5 L155 23.5 L156 24.5 L160 24.5 Q161 25.5 164.5 26 Q168 26.5 169.5 27.5 Q171 28.5 173.5 29 Q176 29.5 180.5 32 L185 34.5 L187.5 34 L185.5 30 L184 29.5 L183 27.5 L182 27.5 L173 19.5 Q172 19.5 168 16.5 Q164 13.5 160 11.5 Q156 9.5 155 9.5 Q154 9.5 151.5 8 Q149 6.5 147.5 6.5 Q146 6.5 144 5.5 Q142 4.5 138 4 Q134 3.5 133.5 3 L133 2.5 L127 2.5 L126 1.5 Q107 1.5 106.5 1.5 Z';

const CARE_PATH =
    'M308.5 3 L308 3.5 L300 3.5 L299 4.5 L295 4.5 L294 5.5 L291 5.5 L290 6.5 L287 6.5 Q286 7.5 282 8.5 Q278 9.5 272 12.5 Q266 15.5 265 16.5 Q264 17.5 262.5 18 Q261 18.5 259.5 20 Q258 21.5 256.5 22 Q255 22.5 253.5 24 L252 25.5 L251 25.5 L248 28.5 L247 28.5 L243 32.5 L242 32.5 L227.5 47 L227.5 48 Q222.5 53 221.5 55 Q220.5 57 219 58.5 L217.5 60 L217.5 61 Q213.5 66 212.5 68.5 Q211.5 71 210 73 Q208.5 75 208.5 76 Q208.5 77 207 79.5 Q205.5 82 205.5 83 Q205.5 84 204.5 85.5 Q203.5 87 203.5 88 Q203.5 89 202.5 90.5 Q201.5 92 201.5 93 Q201.5 94 200.5 96 Q199.5 98 199.5 99.5 L199.5 101 L198.5 102 L198.5 104 L197.5 105 L197.5 109 L196.5 110 L196.5 114 L195.5 115 L195.5 121 L194.5 122 L194.5 134 L193.5 135 L193.5 153 L194.5 154 L194.5 161 L195.5 162 Q195.5 171 196.5 173 Q197.5 175 197.5 176.5 L197.5 178 Q198.5 179 199 182.5 Q199.5 186 201.5 190 Q203.5 194 203.5 195.5 Q203.5 197 208.5 207 Q213.5 217 215 218.5 L216.5 220 L216.5 221 L219.5 224 L219.5 225 L223.5 229 L223.5 230 L233 239.5 L234 239.5 Q236 242.5 237.5 243 Q239 243.5 240.5 245 Q242 246.5 243.5 247 Q245 247.5 248.5 250 Q252 252.5 253 252.5 Q254 252.5 257.5 254.5 Q261 256.5 261.5 256.5 L262 256.5 L263.5 255 L263.5 254 L259.5 250 L259.5 247 L257.5 245 L257.5 241 L256.5 240 L256.5 234 L255.5 233 L255.5 226 L256.5 225 L256.5 220 L257.5 219 L257.5 214 Q258.5 213 259 210 Q259.5 207 261 204.5 Q262.5 202 262.5 201 Q262.5 200 263.5 198.5 Q264.5 197 264.5 196 Q264.5 195 266 193 Q267.5 191 267.5 190 Q267.5 189 269.5 185.5 Q271.5 182 271.5 181 Q271.5 180 273 177.5 Q274.5 175 274.5 174 Q274.5 173 276.5 169.5 Q278.5 166 278.5 164.5 L278.5 163 L279.5 162 Q279.5 160 280.5 158 Q281.5 156 281.5 154 Q281.5 152 282.5 150 Q283.5 148 283.5 137 L283.5 126 Q282.5 125 282 121 Q281.5 117 279 111.5 Q276.5 106 275.5 105 Q274.5 104 272.5 100.5 Q270.5 97 268 94.5 L265.5 92 L265.5 91 Q261.5 88 261.2 87.2 L261 86.5 L260 86.5 Q254 80.5 253 80 L252 79.5 Q247.5 84 243 92 Q238.5 100 238.5 101 Q238.5 102 237 105 Q235.5 108 235.5 109.5 Q235.5 111 234.5 113 Q233.5 115 233 119 Q232.5 123 232 123.5 L231.5 124 L231.5 128 L230.5 129 L230.5 134 L229.5 135 L229.5 145 L228.5 146 L228.5 167 L227.5 168 L228.5 169 L228 186.5 Q224.5 179 224.5 177.5 L224.5 176 L223.5 175 L223.5 173 L222.5 172 L222.5 168 L221.5 167 L221.5 163 L220.5 162 L220.5 154 L219.5 153 L219.5 123 L220.5 122 L220.5 115 L221.5 114 Q222.5 103 223.5 101.5 Q224.5 100 224.5 99 L224.5 98 Q225.5 97 226.5 93 Q227.5 89 228.5 87.5 Q229.5 86 229.5 85 L229.5 84 Q231.5 82 233 78.5 Q234.5 75 235.5 74 Q236.5 73 237.5 71 Q238.5 69 240 67.5 L241.5 66 L241.5 65 L252 54.5 L253 54.5 L256 51.5 L257 51.5 Q262 47.5 267 45 Q272 42.5 273 42.5 Q274 42.5 275.5 41.5 Q277 40.5 278.5 40.5 L280 40.5 Q281 39.5 284 39 Q287 38.5 287.5 38 L288 37.5 L291 37.5 L292 36.5 L299 36.5 L300 35.5 L315 35.5 L316 36.5 L323 36.5 L324 37.5 Q333 38.5 336.5 40.5 Q340 42.5 341 42.5 Q342 42.5 344 43.5 Q346 44.5 347 45.5 Q348 46.5 349.5 47 Q351 47.5 355 51 Q359 54.5 360.5 55 L362 55.5 Q362.5 57 368.5 63 Q374.5 69 375 70.5 Q375.5 72 377 73 L378.5 74 Q378.5 75 381.5 79 Q384.5 83 385.5 85.5 Q386.5 88 387.5 89 Q388.5 90 389 92 Q389.5 94 391.5 98 Q393.5 102 394.5 103 L395.5 104 Q395.5 108 397 110.5 Q398.5 113 399 117.5 L399.5 122 L400.5 123 L400.5 126 L401.5 127 L401.5 133 L402.5 134 L402.5 152 L401.5 153 L401.5 161 L400.5 162 L399.5 171 L398.5 172 L398.5 175 L397.5 176 L397.5 181 L395.5 183 L395.5 186 L394.5 187 L394.5 189 Q392.5 191 392 194 Q391.5 197 389 200.5 Q386.5 204 383 211 Q379.5 218 378 219.5 Q376.5 221 376 222.5 Q375.5 224 374 225.5 L372.5 227 L372.5 228 L369.5 231 L369.5 232 L363.5 238 L363.5 239 L362 240.5 L361 240.5 L358.5 243 L358.5 244 L341 260.5 L340 260.5 L335 265.5 L334 265.5 L328 270.5 L327 270.5 Q323 274.5 321 275.5 Q319 276.5 318.5 277.5 L318 278.5 L317 278.5 L309 285.5 L308 285.5 L302 290.5 L301 290.5 L298 294.5 L296 294.5 L290 300.5 L289 300.5 L285 304.5 L284 304.5 Q283.5 306 281.2 308.2 L279 310.5 L278 310.5 L276.5 312 L276.5 313 L267.5 322 L267.5 323 L261.5 328 L261.5 329 Q258.5 332 256.5 335.5 Q254.5 339 253 340.5 L251.5 342 L251.5 343 Q247.5 348 243 357 Q238.5 366 237 368 Q235.5 370 235.5 371 Q235.5 372 234.5 373.5 Q233.5 375 233 379 Q232.5 383 232 383.5 L231.5 384 L233 385.5 L234.5 385 Q235.5 381 237.5 379 Q239.5 377 241 374 Q242.5 371 247 366 L251.5 361 L251.5 360 L253 358.5 L254 358.5 Q254.5 357 259.8 351.8 L265 346.5 L266 346.5 Q269 342.5 271 341.5 Q273 340.5 275 338.5 L277 336.5 L278 336.5 Q282 332.5 284 331.5 Q286 330.5 288 328.5 L290 326.5 L292 326.5 L297 321.5 L298 321.5 L301 318.5 L302 318.5 Q306 314.5 307.5 314 Q309 313.5 310.5 312 L312 310.5 L314 310.5 Q317 306.5 320.5 304.5 Q324 302.5 326 300.5 Q328 298.5 329.5 298 L331 297.5 L332 294.5 L334 294.5 Q341 288.5 344 287 Q347 285.5 348 284 L349 282.5 L350 282.5 L354 278.5 L355 278.5 L361 272.5 L362 272.5 L365 269.5 L366 269.5 L368 266.5 L369 266.5 L372 263.5 L373 263.5 L375.5 261 L375.5 260 L382.5 254 L382.5 253 L387.5 248 L387.5 247 L389 246.5 Q389.5 245 392.5 242 Q395.5 239 396 237.5 Q396.5 236 398.5 234 L400.5 232 L400.5 231 Q406.5 225 407.5 222.5 Q408.5 220 410 218.5 L411.5 217 L411.5 215 Q416.5 209 417 207 Q417.5 205 419 203 Q420.5 201 421 199 Q421.5 197 423 194.5 Q424.5 192 424.5 191 Q424.5 190 426 188 Q427.5 186 427.5 184.5 L427.5 183 L429.5 181 L429.5 179 L430.5 178 L430.5 176 L431.5 175 Q431.5 173 432.5 171 Q433.5 169 433.5 166 L433.5 163 L434.5 162 L434.5 159 L435.5 158 L435.5 155 L436.5 154 L436.5 146 L437.5 145 L437.5 121 L436.5 120 L436.5 113 L435.5 112 L435.5 107 L434.5 106 L434.5 102 L433.5 101 Q433.5 98 432.5 96 Q431.5 94 431 90.5 Q430.5 87 429.5 86 L428.5 85 L428.5 82 Q427.5 81 427 78.5 Q426.5 76 425.5 74.5 Q424.5 73 424.5 72 Q424.5 71 421.5 65 Q418.5 59 417.5 58 Q416.5 57 416 55 Q415.5 53 413.5 51 Q411.5 49 410.5 47 Q409.5 45 404 39 Q398.5 33 398.2 32.2 L398 31.5 L397 31.5 Q390 24.5 388 23.5 Q386 22.5 384 20.5 Q382 18.5 380.5 18 Q379 17.5 378 16.5 Q377 15.5 372 13 Q367 10.5 364.5 10 Q362 9.5 359 8 Q356 6.5 352 6 Q348 5.5 347.5 5 L347 4.5 L343 4.5 L342 3.5 L337 3.5 L336 2.5 Q310 2.5 309.5 2.5 Z';

const HEAD = { cx: 99.5, cy: 96.5, r: 27 };

const CROSS = [
    { x: 323.5, y: 132.5, width: 25, height: 81 },
    { x: 295.5, y: 160.5, width: 81, height: 25 },
];

/** The mark on its own, sized by the parent. */
export function SanaGoMark({
    className,
    title = 'SanaGo',
}: {
    className?: string;
    title?: string;
}) {
    // React's ids can carry characters that are awkward inside a
    // `url(#fragment)` reference, so keep them to a safe alphabet.
    const uid = useId().replace(/[^a-zA-Z0-9_-]/g, '');
    const figure = `sg-figure-${uid}`;
    const care = `sg-care-${uid}`;

    return (
        <svg
            viewBox={MARK_VIEWBOX}
            className={className}
            role="img"
            aria-label={title}
        >
            <defs>
                <linearGradient
                    id={figure}
                    x1="0"
                    y1="0"
                    x2="0"
                    y2="392"
                    gradientUnits="userSpaceOnUse"
                >
                    <stop offset="0" stopColor="#0074bc" />
                    <stop offset="1" stopColor="#2ca8dc" />
                </linearGradient>
                <linearGradient
                    id={care}
                    x1="0"
                    y1="0"
                    x2="0"
                    y2="392"
                    gradientUnits="userSpaceOnUse"
                >
                    <stop offset="0" stopColor="#40b448" />
                    <stop offset="1" stopColor="#88c03c" />
                </linearGradient>
            </defs>

            <path d={FIGURE_PATH} fill={`url(#${figure})`} />
            <circle
                cx={HEAD.cx}
                cy={HEAD.cy}
                r={HEAD.r}
                fill={`url(#${figure})`}
            />
            <path d={CARE_PATH} fill={`url(#${care})`} />
            <g fill={`url(#${care})`}>
                {CROSS.map((bar) => (
                    <rect key={`${bar.x}-${bar.y}`} {...bar} />
                ))}
            </g>
        </svg>
    );
}

export type SanaGoMarkSize = 'xs' | 'sm' | 'md' | 'lg' | 'xl';

/** Heights only: the mark is wider than it is tall, so width follows. */
const markSizes: Record<SanaGoMarkSize, string> = {
    xs: 'h-5 w-auto',
    sm: 'h-6 w-auto',
    md: 'h-7 w-auto',
    lg: 'h-9 w-auto',
    xl: 'h-14 w-auto',
};

const wordSizes: Record<SanaGoMarkSize, string> = {
    xs: 'text-[12px]',
    sm: 'text-[13.5px]',
    md: 'text-[15px]',
    lg: 'text-[18px]',
    xl: 'text-[26px]',
};

/**
 * The wordmark. "Sana" takes the figure's blue and "Go" the heart's green, so
 * the name reads as the same two halves as the mark.
 */
export function SanaGoWordmark({
    size = 'sm',
    className,
}: {
    size?: SanaGoMarkSize;
    className?: string;
}) {
    return (
        <span
            className={cn(
                'font-semibold tracking-[-0.02em] whitespace-nowrap',
                wordSizes[size],
                className,
            )}
        >
            <span className="text-brand-figure-ink">Sana</span>
            <span className="text-brand-care-ink">Go</span>
        </span>
    );
}

export interface SanaGoLockupProps {
    size?: SanaGoMarkSize;
    /** Eyebrow under the wordmark; omit for the bare lockup. */
    caption?: ReactNode;
    /** Render the mark only, with the wordmark hidden (collapsed rails). */
    compact?: boolean;
    href?: string;
    className?: string;
}

/**
 * Mark plus wordmark: the one block every shell mounts. It never re-declares
 * the mark or the wordmark, so a change to the identity happens in exactly one
 * file.
 */
export function SanaGoLockup({
    size = 'sm',
    caption,
    compact = false,
    href,
    className,
}: SanaGoLockupProps) {
    const content = (
        <>
            <SanaGoMark className={cn('shrink-0', markSizes[size])} />
            {!compact && (
                <span className="min-w-0 overflow-hidden text-left">
                    <SanaGoWordmark size={size} className="block truncate" />
                    {caption && (
                        <span className="mt-0.5 block truncate text-[9.5px] leading-none font-medium tracking-[0.18em] text-ink-subtle uppercase">
                            {caption}
                        </span>
                    )}
                </span>
            )}
        </>
    );

    const base = cn(
        'group inline-flex min-w-0 items-center gap-2.5 rounded-md transition-opacity duration-200 ease-premium',
        className,
    );

    if (!href) {
        return <div className={base}>{content}</div>;
    }

    return (
        <Link href={href} className={cn(base, 'hover:opacity-80')}>
            {content}
        </Link>
    );
}
