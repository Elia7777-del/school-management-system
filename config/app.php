<?php
/**
 * Application Configuration
 * 
 * Defines application-wide constants and grading functions
 * for the Tanzanian education system.
 * 
 * @package SchoolManagementSystem
 */

// ─── Application Constants ──────────────────────────────────────
define('APP_NAME', 'School Management System');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/school-management-system/public');
define('APP_ROOT', dirname(__DIR__));
define('ITEMS_PER_PAGE', 10);

// ─── Tanzanian Grading — Primary (Standard I–VII) ──────────────
/**
 * Calculate grade for primary education (Std I-VII).
 *
 * @param float $marks Student's marks (0-100)
 * @return string Grade letter (A, B, C, D, F)
 */
function getGradePrimary(float $marks): string
{
    if ($marks >= 81) return 'A';
    if ($marks >= 61) return 'B';
    if ($marks >= 41) return 'C';
    if ($marks >= 21) return 'D';
    return 'F';
}

/**
 * Get remarks for primary education grade.
 *
 * @param string $grade Grade letter
 * @return string Remarks text
 */
function getRemarksPrimary(string $grade): string
{
    $remarks = [
        'A' => 'Excellent',
        'B' => 'Very Good',
        'C' => 'Good',
        'D' => 'Satisfactory',
        'F' => 'Fail',
    ];
    return $remarks[$grade] ?? 'N/A';
}

// ─── Tanzanian Grading — Secondary (Form I–IV) ─────────────────
/**
 * Calculate grade for secondary education (Form I-IV).
 *
 * @param float $marks Student's marks (0-100)
 * @return array ['grade' => string, 'points' => int, 'remarks' => string]
 */
function getGradeSecondary(float $marks): array
{
    if ($marks >= 75) return ['grade' => 'A', 'points' => 1, 'remarks' => 'Excellent'];
    if ($marks >= 65) return ['grade' => 'B+', 'points' => 2, 'remarks' => 'Very Good'];
    if ($marks >= 55) return ['grade' => 'B', 'points' => 3, 'remarks' => 'Good'];
    if ($marks >= 45) return ['grade' => 'C', 'points' => 4, 'remarks' => 'Satisfactory'];
    if ($marks >= 30) return ['grade' => 'D', 'points' => 5, 'remarks' => 'Pass'];
    return ['grade' => 'F', 'points' => 7, 'remarks' => 'Fail'];
}

/**
 * Calculate Division for secondary education based on best 7 subjects.
 *
 * @param array $points Array of subject points
 * @return string Division (I, II, III, IV, 0)
 */
function calculateDivision(array $points): string
{
    // Sort points ascending and take best 7
    sort($points);
    $best7 = array_slice($points, 0, min(7, count($points)));
    $totalPoints = array_sum($best7);

    if ($totalPoints >= 7 && $totalPoints <= 17) return 'I';
    if ($totalPoints >= 18 && $totalPoints <= 21) return 'II';
    if ($totalPoints >= 22 && $totalPoints <= 25) return 'III';
    if ($totalPoints >= 26 && $totalPoints <= 33) return 'IV';
    return '0'; // Fail
}
