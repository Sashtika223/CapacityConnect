import { Request, Response } from 'express';
import { prisma } from '../prisma/client';

export class VerifyController {
  /**
   * Public Certificate Verification Endpoint: GET /verify/:code
   */
  public static async verifyCertificate(req: Request, res: Response): Promise<void> {
    const { code } = req.params;

    const certificate = await prisma.certificate.findFirst({
      where: {
        OR: [
          { verificationCode: code },
          { certificateNumber: code },
          { id: code }
        ]
      },
      include: {
        user: {
          select: {
            id: true,
            name: true,
            organization: true,
            department: true,
            designation: true
          }
        },
        course: {
          select: {
            id: true,
            title: true,
            code: true,
            subject: true,
            durationHours: true,
            trainer: { select: { name: true, designation: true } }
          }
        }
      }
    });

    if (!certificate) {
      res.status(404).json({
        success: false,
        valid: false,
        message: 'Invalid Certificate: No official IMD / MoES capacity record found for this verification code.'
      });
      return;
    }

    if (certificate.isRevoked) {
      res.status(200).json({
        success: true,
        valid: false,
        status: 'REVOKED',
        message: 'This certificate was revoked by IMD administration.',
        certificate: {
          certificateNumber: certificate.certificateNumber,
          issueDate: certificate.issueDate
        }
      });
      return;
    }

    res.json({
      success: true,
      valid: true,
      status: 'VERIFIED',
      message: 'Official Government of India (IMD/MoES) Capacity Building Certificate Verified',
      certificate: {
        certificateNumber: certificate.certificateNumber,
        verificationCode: certificate.verificationCode,
        recipientName: certificate.user.name,
        recipientOrganization: certificate.user.organization,
        recipientDepartment: certificate.user.department,
        recipientDesignation: certificate.user.designation,
        courseTitle: certificate.course.title,
        courseCode: certificate.course.code,
        courseSubject: certificate.course.subject,
        durationHours: certificate.course.durationHours,
        instructorName: certificate.course.trainer.name,
        grade: certificate.grade,
        issueDate: certificate.issueDate,
        qrCodeDataUrl: certificate.qrCodeDataUrl,
        pdfUrl: certificate.pdfUrl
      }
    });
  }
}
