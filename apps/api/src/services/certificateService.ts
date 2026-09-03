import QRCode from 'qrcode';
import { PDFDocument, rgb, StandardFonts } from 'pdf-lib';
import fs from 'fs';
import path from 'path';
import crypto from 'crypto';
import { prisma } from '../prisma/client';
import { CONFIG } from '../config';

export class CertificateService {
  /**
   * Auto-generates a verifiable Certificate for a course completion
   */
  public static async generateCertificate(
    enrollmentId: string,
    userId: string,
    courseId: string,
    scorePercentage: number
  ): Promise<any> {
    // 1. Check if certificate already exists
    const existing = await prisma.certificate.findUnique({
      where: { enrollmentId }
    });
    if (existing) return existing;

    // 2. Fetch User and Course details
    const user = await prisma.user.findUnique({ where: { id: userId } });
    const course = await prisma.course.findUnique({
      where: { id: courseId },
      include: { trainer: true }
    });

    if (!user || !course) {
      throw new Error('User or Course not found for certificate generation');
    }

    const certId = crypto.randomUUID();
    const verificationCode = `IMD-${crypto.randomBytes(4).toString('hex').toUpperCase()}`;
    const certificateNumber = `IMD-CERT-2026-${crypto.randomBytes(3).toString('hex').toUpperCase()}`;

    // Grade calculation
    let grade = 'Pass';
    if (scorePercentage >= 90) grade = 'Distinction with Honors';
    else if (scorePercentage >= 80) grade = 'Distinction';
    else if (scorePercentage >= 70) grade = 'First Class';

    // 3. Generate QR code linking to public verification endpoint
    const verificationUrl = `${CONFIG.CLIENT_URL}/verify/${verificationCode}`;
    const qrCodeDataUrl = await QRCode.toDataURL(verificationUrl, {
      margin: 2,
      width: 250,
      color: {
        dark: '#082849',
        light: '#FFFFFF'
      }
    });

    // 4. Generate PDF using pdf-lib
    const pdfDoc = await PDFDocument.create();
    const page = pdfDoc.addPage([842, 595]); // Landscape A4

    const fontBold = await pdfDoc.embedFont(StandardFonts.HelveticaBold);
    const fontRegular = await pdfDoc.embedFont(StandardFonts.Helvetica);
    const fontOblique = await pdfDoc.embedFont(StandardFonts.HelveticaOblique);

    // Background styling & border
    page.drawRectangle({
      x: 20,
      y: 20,
      width: 802,
      height: 555,
      borderColor: rgb(0.04, 0.44, 0.78), // IMD blue
      borderWidth: 4,
      color: rgb(0.98, 0.99, 1.0)
    });

    page.drawRectangle({
      x: 30,
      y: 30,
      width: 782,
      height: 535,
      borderColor: rgb(0.28, 0.9, 0.76), // MoES accent
      borderWidth: 1.5
    });

    // Header Titles
    page.drawText('GOVERNMENT OF INDIA | MINISTRY OF EARTH SCIENCES (MoES)', {
      x: 180,
      y: 530,
      size: 13,
      font: fontBold,
      color: rgb(0.03, 0.25, 0.43)
    });

    page.drawText('INDIA METEOROLOGICAL DEPARTMENT (IMD)', {
      x: 230,
      y: 508,
      size: 16,
      font: fontBold,
      color: rgb(0.05, 0.55, 0.91)
    });

    page.drawText('CERTIFICATE OF CAPACITY BUILDING & EXCELLENCE', {
      x: 190,
      y: 460,
      size: 18,
      font: fontBold,
      color: rgb(0.08, 0.15, 0.28)
    });

    page.drawText('This is proudly presented to', {
      x: 330,
      y: 420,
      size: 12,
      font: fontOblique,
      color: rgb(0.3, 0.35, 0.4)
    });

    // Trainee Name
    const nameWidth = fontBold.widthOfTextAtSize(user.name.toUpperCase(), 24);
    page.drawText(user.name.toUpperCase(), {
      x: (842 - nameWidth) / 2,
      y: 380,
      size: 24,
      font: fontBold,
      color: rgb(0.02, 0.44, 0.78)
    });

    const orgText = `${user.designation || 'Officer / Scientist'}, ${user.department || 'IMD Operational Division'}`;
    const orgWidth = fontRegular.widthOfTextAtSize(orgText, 12);
    page.drawText(orgText, {
      x: (842 - orgWidth) / 2,
      y: 355,
      size: 12,
      font: fontRegular,
      color: rgb(0.2, 0.25, 0.3)
    });

    // Course completion text
    const descText = `has successfully completed the capacity training programme on`;
    page.drawText(descText, {
      x: 240,
      y: 320,
      size: 12,
      font: fontRegular,
      color: rgb(0.3, 0.35, 0.4)
    });

    const courseTitle = `"${course.title}" (${course.code})`;
    const titleWidth = fontBold.widthOfTextAtSize(courseTitle, 16);
    page.drawText(courseTitle, {
      x: (842 - titleWidth) / 2,
      y: 290,
      size: 16,
      font: fontBold,
      color: rgb(0.08, 0.15, 0.28)
    });

    const detailsText = `Grade Achieved: ${grade} (${scorePercentage.toFixed(1)}%) | Duration: ${course.durationHours} Hours`;
    const detailsWidth = fontBold.widthOfTextAtSize(detailsText, 12);
    page.drawText(detailsText, {
      x: (842 - detailsWidth) / 2,
      y: 260,
      size: 12,
      font: fontBold,
      color: rgb(0.06, 0.6, 0.4)
    });

    // Signatures & Metadata Footer
    page.drawText(`Date of Issue: ${new Date().toLocaleDateString('en-IN', { year: 'numeric', month: 'long', day: 'numeric' })}`, {
      x: 60,
      y: 110,
      size: 10,
      font: fontRegular,
      color: rgb(0.3, 0.35, 0.4)
    });

    page.drawText(`Certificate ID: ${certificateNumber}`, {
      x: 60,
      y: 90,
      size: 10,
      font: fontRegular,
      color: rgb(0.3, 0.35, 0.4)
    });

    page.drawText(`Verification Code: ${verificationCode}`, {
      x: 60,
      y: 70,
      size: 10,
      font: fontBold,
      color: rgb(0.02, 0.44, 0.78)
    });

    // Embed QR Code into PDF
    const qrPngBuffer = Buffer.from(qrCodeDataUrl.split(',')[1], 'base64');
    const qrImage = await pdfDoc.embedPng(qrPngBuffer);
    page.drawImage(qrImage, {
      x: 680,
      y: 55,
      width: 90,
      height: 90
    });

    page.drawText('Scan to Verify', {
      x: 695,
      y: 45,
      size: 8,
      font: fontBold,
      color: rgb(0.3, 0.35, 0.4)
    });

    // Save PDF to uploads directory
    const uploadDir = path.resolve(process.cwd(), 'uploads/certificates');
    if (!fs.existsSync(uploadDir)) {
      fs.mkdirSync(uploadDir, { recursive: true });
    }

    const pdfFilename = `${certificateNumber}.pdf`;
    const pdfFilePath = path.join(uploadDir, pdfFilename);
    const pdfBytes = await pdfDoc.save();
    fs.writeFileSync(pdfFilePath, pdfBytes);

    const pdfUrl = `/uploads/certificates/${pdfFilename}`;

    // 5. Save Certificate record in DB
    const certificate = await prisma.certificate.create({
      data: {
        id: certId,
        certificateNumber,
        enrollmentId,
        userId,
        courseId,
        issueDate: new Date(),
        grade,
        qrCodeDataUrl,
        pdfUrl,
        verificationCode
      },
      include: {
        user: { select: { id: true, name: true, email: true, department: true } },
        course: { select: { id: true, title: true, code: true } }
      }
    });

    return certificate;
  }
}
