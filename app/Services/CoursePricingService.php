<?php

namespace App\Services;

use App\Models\Course;

class CoursePricingService
{
    /**
     * NYSC Course Pricing
     */
    public static function getNyscPricing()
    {
        return [
            1 => ['price' => 10000, 'installment' => 1, 'duration' => '4 weeks'],
            2 => ['price' => 18000, 'installment' => 2, 'duration' => '4 weeks'],
            3 => ['price' => 18000, 'installment' => 2, 'duration' => '4 weeks'],
            4 => ['price' => 25000, 'installment' => 3, 'duration' => '8 weeks'],
            5 => ['price' => 25000, 'installment' => 3, 'duration' => '8 weeks'],
            6 => ['price' => 25000, 'installment' => 3, 'duration' => '8 weeks'],
            7 => ['price' => 30000, 'installment' => 3, 'duration' => '12 weeks'],
            8 => ['price' => 30000, 'installment' => 3, 'duration' => '12 weeks'],
            9 => ['price' => 30000, 'installment' => 3, 'duration' => '12 weeks'],
        ];
    }

    /**
     * Working-Class Course Pricing
     */
    public static function getWorkingClassPricing()
    {
        return [
            'Project Management' => 70000,
            'Human Resource Management' => 50000,
            'Data Analysis' => 50000,
            'Public Speaking & Presentation' => 50000,
            'Virtual Assistant' => 50000,
            'ICT & Computer Fundamentals' => 50000,
            'Digital Marketing' => 50000,
            'HSE Level 1 & 2' => 50000,
            'Entrepreneurship & Business Development' => 40000,
        ];
    }

    /**
     * Calculate discount for Working-Class based on number of courses
     */
    public static function getWorkingClassDiscount($courseCount)
    {
        if ($courseCount == 1) {
            return 0; // No discount
        } elseif ($courseCount >= 2 && $courseCount <= 3) {
            return 5; // 5% discount
        } elseif ($courseCount >= 4 && $courseCount <= 6) {
            return 15; // 15% discount
        } elseif ($courseCount >= 7 && $courseCount <= 9) {
            return 25; // 25% discount
        }
        return 0;
    }

    /**
     * Check if installment is allowed for Working-Class
     */
    public static function isInstallmentAllowed($courseCount)
    {
        return $courseCount > 1; // Only allowed if more than 1 course
    }

    /**
     * Calculate total price for NYSC courses
     */
    public static function calculateNyscTotal($courseNumbers)
    {
        $pricing = self::getNyscPricing();
        $total = 0;
        
        foreach ($courseNumbers as $courseNum) {
            if (isset($pricing[$courseNum])) {
                $total += $pricing[$courseNum]['price'];
            }
        }
        
        return $total;
    }

    /**
     * Calculate total price for Working-Class courses with discount
     */
    public static function calculateWorkingClassTotal($courseTitles)
    {
        $pricing = self::getWorkingClassPricing();
        $subtotal = 0;
        
        foreach ($courseTitles as $title) {
            if (isset($pricing[$title])) {
                $subtotal += $pricing[$title];
            }
        }
        
        $courseCount = count($courseTitles);
        $discountPercent = self::getWorkingClassDiscount($courseCount);
        $discount = ($subtotal * $discountPercent) / 100;
        $total = $subtotal - $discount;
        
        return [
            'subtotal' => $subtotal,
            'discount_percent' => $discountPercent,
            'discount' => $discount,
            'total' => $total,
            'installment_allowed' => self::isInstallmentAllowed($courseCount),
        ];
    }

    /**
     * Get installment plan for NYSC course
     */
    public static function getNyscInstallmentPlan($courseNum)
    {
        $pricing = self::getNyscPricing();
        if (!isset($pricing[$courseNum])) {
            return null;
        }
        
        $price = $pricing[$courseNum]['price'];
        $installmentCount = $pricing[$courseNum]['installment'];
        $installmentAmount = round($price / $installmentCount, 2);
        
        return [
            'total' => $price,
            'installment_count' => $installmentCount,
            'installment_amount' => $installmentAmount,
        ];
    }
}

