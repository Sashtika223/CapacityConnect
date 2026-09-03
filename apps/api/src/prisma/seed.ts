import bcrypt from 'bcryptjs';
import QRCode from 'qrcode';
import crypto from 'crypto';
import { prisma } from './client';
import { Role, ApprovalStatus, CourseLevel, CourseStatus, EnrollmentStatus, ResourceFormat, QuestionDifficulty, SentimentTag, NotificationType } from '@capacity-connect/shared-types';
import { SentimentAnalyzer } from '../services/sentimentAnalyzer';

async function main() {
  console.log('🌱 Starting CAPACITY CONNECT Database Seed (IMD / MoES)...');

  // Clear existing data cleanly in cascade order
  await prisma.auditLog.deleteMany();
  await prisma.certificate.deleteMany();
  await prisma.assessmentAttempt.deleteMany();
  await prisma.option.deleteMany();
  await prisma.question.deleteMany();
  await prisma.questionnaire.deleteMany();
  await prisma.feedback.deleteMany();
  await prisma.resourceAccessLog.deleteMany();
  await prisma.learningResource.deleteMany();
  await prisma.enrollment.deleteMany();
  await prisma.course.deleteMany();
  await prisma.competencyMap.deleteMany();
  await prisma.achievement.deleteMany();
  await prisma.notification.deleteMany();
  await prisma.profile.deleteMany();
  await prisma.user.deleteMany();

  const defaultPassword = await bcrypt.hash('Password@123', 10);
  const adminPassword = await bcrypt.hash('Admin@123', 10);

  // 1. Create Users
  console.log('Creating Admin, Trainers, and Trainees...');

  const admin = await prisma.user.create({
    data: {
      email: 'admin@imd.gov.in',
      name: 'Dr. M. Mohapatra',
      passwordHash: adminPassword,
      role: Role.ADMIN,
      approvalStatus: ApprovalStatus.APPROVED,
      employeeId: 'IMD-ADM-001',
      department: 'Director General of Meteorology (DGM)',
      designation: 'Director General & Chief Administrator',
      organization: 'India Meteorological Department (IMD)',
      profile: {
        create: {
          qualifications: 'Ph.D. in Meteorology, D.Sc.',
          experienceYears: 30,
          bio: 'Executive Director managing nationwide capacity building and numerical meteorological frameworks.',
          skills: ['Strategic Planning', 'Tropical Cyclones', 'Disaster Mitigation', 'Atmospheric Modeling'],
          interests: ['AI in Weather Forecasting', 'Early Warning Systems', 'Regional Radar Networks']
        }
      }
    }
  });

  const trainer1 = await prisma.user.create({
    data: {
      email: 'trainer.radar@imd.gov.in',
      name: 'Dr. S. K. Roy',
      passwordHash: defaultPassword,
      role: Role.TRAINER,
      approvalStatus: ApprovalStatus.APPROVED,
      employeeId: 'IMD-TRN-101',
      department: 'Radar Meteorology Division',
      designation: 'Scientist "F" & Radar Systems Lead',
      organization: 'India Meteorological Department (IMD)',
      profile: {
        create: {
          qualifications: 'Ph.D. Atmospheric Radar Systems, IIT Kharagpur',
          experienceYears: 18,
          bio: 'Specialist in S-band and C-band Doppler Weather Radar operations, nowcasting, and convective storm tracking.',
          skills: ['Doppler Radar', 'Nowcasting', 'Severe Convection', 'Dual-Pol Radar', 'Python'],
          interests: ['Urban Flash Flood Forecasting', 'Radar Signal Processing', 'Microburst Detection']
        }
      }
    }
  });

  const trainer2 = await prisma.user.create({
    data: {
      email: 'trainer.satellite@imd.gov.in',
      name: 'Dr. Ananya Sharma',
      passwordHash: defaultPassword,
      role: Role.TRAINER,
      approvalStatus: ApprovalStatus.APPROVED,
      employeeId: 'IMD-TRN-102',
      department: 'Satellite Meteorology Division',
      designation: 'Scientist "E" (Space Applications)',
      organization: 'India Meteorological Department (IMD)',
      profile: {
        create: {
          qualifications: 'Ph.D. Remote Sensing, ISRO/IIRS',
          experienceYears: 14,
          bio: 'Expert in geostationary meteorological satellite interpretation (INSAT-3D/3DR), water vapor tracers, and cyclone intensity estimation.',
          skills: ['INSAT-3D/3DR', 'Remote Sensing', 'Dvorak Technique', 'Cloud Physics', 'GIS'],
          interests: ['Geostationary Imagery', 'Sounder Profiles', 'Tropical Cyclogenesis']
        }
      }
    }
  });

  const trainer3 = await prisma.user.create({
    data: {
      email: 'trainer.nwp@imd.gov.in',
      name: 'Dr. Rajesh Mukherjee',
      passwordHash: defaultPassword,
      role: Role.TRAINER,
      approvalStatus: ApprovalStatus.APPROVED,
      employeeId: 'IMD-TRN-103',
      department: 'Numerical Weather Prediction (NWP)',
      designation: 'Scientist "E" (High Performance Computing)',
      organization: 'Ministry of Earth Sciences (MoES)',
      profile: {
        create: {
          qualifications: 'Ph.D. Computational Physics, IISc Bangalore',
          experienceYears: 12,
          bio: 'Specialist in WRF modeling, global GFS ensemble prediction, high-performance computing clusters, and data assimilation.',
          skills: ['WRF Model', 'Data Assimilation', 'HPC Clusters', 'Fortran', 'Python', 'Linux'],
          interests: ['Ensemble Prediction Systems', 'Machine Learning Parameterizations']
        }
      }
    }
  });

  // Pending Trainer for Admin queue verification
  const pendingTrainer = await prisma.user.create({
    data: {
      email: 'trainer.pending@imd.gov.in',
      name: 'Dr. Vikramaditya Rathore',
      passwordHash: defaultPassword,
      role: Role.TRAINER,
      approvalStatus: ApprovalStatus.PENDING,
      employeeId: 'IMD-PEND-501',
      department: 'Seismology & Earth System Dynamics',
      designation: 'Scientist "D"',
      organization: 'Ministry of Earth Sciences (MoES)',
      profile: {
        create: {
          qualifications: 'Ph.D. Geophysics',
          experienceYears: 8,
          bio: 'Applies seismic network arrays and geophysical anomaly analysis for tectonic monitoring.',
          skills: ['Seismology', 'Geophysics', 'Sensor Arrays'],
          interests: ['Tsunami Early Warning', 'Crustal Deformation']
        }
      }
    }
  });

  // Trainees
  const trainee1 = await prisma.user.create({
    data: {
      email: 'trainee1@imd.gov.in',
      name: 'Rohan Verma',
      passwordHash: defaultPassword,
      role: Role.TRAINEE,
      approvalStatus: ApprovalStatus.APPROVED,
      employeeId: 'IMD-TRN-201',
      department: 'Numerical Weather Prediction (NWP)',
      designation: 'Scientific Assistant',
      organization: 'India Meteorological Department (IMD)',
      profile: {
        create: {
          qualifications: 'M.Sc. Meteorology, Pune University',
          experienceYears: 3,
          bio: 'Early career meteorologist interested in WRF modeling and automated forecast pipelines.',
          skills: ['Python', 'WRF Model', 'Linux', 'Statistical Meteorology'],
          interests: ['Numerical Weather Prediction', 'Machine Learning', 'Data Assimilation']
        }
      }
    }
  });

  const trainee2 = await prisma.user.create({
    data: {
      email: 'trainee2@imd.gov.in',
      name: 'Pooja Nair',
      passwordHash: defaultPassword,
      role: Role.TRAINEE,
      approvalStatus: ApprovalStatus.APPROVED,
      employeeId: 'IMD-TRN-202',
      department: 'Cyclone Warning Division',
      designation: 'Meteorologist-II',
      organization: 'India Meteorological Department (IMD)',
      profile: {
        create: {
          qualifications: 'M.Tech Remote Sensing & GIS',
          experienceYears: 5,
          bio: 'Forecaster at Cyclone Warning Center tracking North Indian Ocean tropical disturbances.',
          skills: ['Radar Analysis', 'INSAT-3D/3DR', 'Dvorak Technique', 'GIS'],
          interests: ['Tropical Cyclones', 'Radar Meteorology', 'Satellite Meteorology']
        }
      }
    }
  });

  const traineeAtRisk = await prisma.user.create({
    data: {
      email: 'trainee.atrisk@imd.gov.in',
      name: 'Suresh Gupta',
      passwordHash: defaultPassword,
      role: Role.TRAINEE,
      approvalStatus: ApprovalStatus.APPROVED,
      employeeId: 'IMD-TRN-203',
      department: 'Agro-Meteorology Division',
      designation: 'Agromet Observer',
      organization: 'India Meteorological Department (IMD)',
      profile: {
        create: {
          qualifications: 'B.Sc. Agriculture',
          experienceYears: 2,
          bio: 'Field staff handling district agromet advisory bulletins.',
          skills: ['Agro-Meteorology', 'Weather Observations'],
          interests: ['Monsoon Forecasting', 'Crop Weather Models']
        }
      }
    }
  });

  // 2. Competency Map for Trainers
  console.log('Mapping Trainer Competencies...');

  await prisma.competencyMap.createMany({
    data: [
      {
        trainerId: trainer1.id,
        subject: 'Radar Meteorology',
        proficiencyScore: 96,
        verified: true,
        evidence: '18 Years DWR Operations, 12 Research Publications, MoES Excellence Award 2024'
      },
      {
        trainerId: trainer1.id,
        subject: 'Severe Storm Nowcasting',
        proficiencyScore: 92,
        verified: true,
        evidence: 'Lead Author for IMD Standard Operating Procedures on Doppler Weather Radar'
      },
      {
        trainerId: trainer2.id,
        subject: 'Satellite Meteorology',
        proficiencyScore: 94,
        verified: true,
        evidence: '14 Years INSAT Operations, ISRO Joint Research Project Lead'
      },
      {
        trainerId: trainer2.id,
        subject: 'Tropical Cyclone Intensity Estimation',
        proficiencyScore: 90,
        verified: true,
        evidence: 'Certified Dvorak Specialist, WMO Region II Trainer'
      },
      {
        trainerId: trainer3.id,
        subject: 'Numerical Weather Prediction (NWP)',
        proficiencyScore: 95,
        verified: true,
        evidence: '12 Years HPC modeling, High Resolution Ensemble Prediction Lead at MoES'
      }
    ]
  });

  // 3. Courses
  console.log('Creating Official IMD/MoES Courses...');

  const course1 = await prisma.course.create({
    data: {
      title: 'Operational Doppler Weather Radar Interpretation & Severe Storm Tracking',
      code: 'IMD-RAD-201',
      description: 'Comprehensive capacity training on Dual-Polarimetric Doppler Weather Radar (DWR) data interpretation, velocity azimuth display (VAD), reflectivity patterns (Z, ZDR, KDP, RhoHV), and real-time convective storm nowcasting for aviation and disaster warning.',
      category: 'Radar & Remote Sensing',
      subject: 'Radar Meteorology',
      targetAudience: 'Operational Forecasters, Scientific Assistants, Aviation Met Officers',
      level: CourseLevel.INTERMEDIATE,
      durationHours: 15,
      tags: ['Radar Meteorology', 'Doppler Radar', 'Nowcasting', 'Severe Convection', 'Dual-Pol Radar'],
      trainerId: trainer1.id,
      passingScore: 70,
      status: CourseStatus.PUBLISHED
    }
  });

  const course2 = await prisma.course.create({
    data: {
      title: 'INSAT-3D/3DR Satellite Data Applications in Tropical Cyclogenesis',
      code: 'IMD-SAT-301',
      description: 'Advanced meteorological analysis of geostationary INSAT-3D/3DR sounder and imager channels, thermal infrared brightness temperatures, Dvorak T-number classification, and rapid intensification indicators over Bay of Bengal and Arabian Sea.',
      category: 'Satellite Meteorology',
      subject: 'Satellite Meteorology',
      targetAudience: 'Cyclone Warning Division, Regional Meteorological Center Officers',
      level: CourseLevel.ADVANCED,
      durationHours: 20,
      tags: ['Satellite Meteorology', 'INSAT-3D/3DR', 'Tropical Cyclones', 'Dvorak Technique', 'Remote Sensing'],
      trainerId: trainer2.id,
      passingScore: 75,
      status: CourseStatus.PUBLISHED
    }
  });

  const course3 = await prisma.course.create({
    data: {
      title: 'Operational Numerical Weather Prediction: WRF Modeling & Ensemble Forecasts',
      code: 'IMD-NWP-101',
      description: 'Core capacity building on configuring, running, and post-processing the Weather Research and Forecasting (WRF) model on HPC clusters, understanding physics parameterizations, and interpreting multi-model ensemble probabilities.',
      category: 'Atmospheric Modeling',
      subject: 'Numerical Weather Prediction (NWP)',
      targetAudience: 'Meteorologists, Scientific Officers, Modelers',
      level: CourseLevel.BEGINNER,
      durationHours: 18,
      tags: ['Numerical Weather Prediction (NWP)', 'WRF Model', 'HPC Clusters', 'Data Assimilation', 'Linux'],
      trainerId: trainer3.id,
      passingScore: 70,
      status: CourseStatus.PUBLISHED
    }
  });

  // 4. Learning Resources in Trainer Library
  console.log('Populating Trainer Library with Study Material...');

  const res1 = await prisma.learningResource.create({
    data: {
      title: 'Doppler Weather Radar Interpretation Guide (S-Band / C-Band)',
      description: 'Official IMD standard operating procedure on interpreting reflectivity hooks, bow echoes, and microburst signatures.',
      format: ResourceFormat.PDF,
      fileUrl: '/uploads/sample-lecture.pdf',
      fileSize: 4500000,
      subject: 'Radar Meteorology',
      tags: ['DWR', 'Reflectivity', 'Hook Echo', 'Nowcasting'],
      courseId: course1.id,
      trainerId: trainer1.id,
      downloadCount: 42
    }
  });

  const res2 = await prisma.learningResource.create({
    data: {
      title: 'INSAT-3DR Rapid Scan RGB Composites & Cyclone Analysis PPT',
      description: 'High-resolution presentation covering true color RGBs, water vapor upper-level divergence, and eye temperature anomalies.',
      format: ResourceFormat.PPT,
      fileUrl: '/uploads/sample-lecture.pdf',
      fileSize: 12400000,
      subject: 'Satellite Meteorology',
      tags: ['INSAT-3DR', 'RGB Composites', 'Cyclone Eye', 'Water Vapor'],
      courseId: course2.id,
      trainerId: trainer2.id,
      downloadCount: 68
    }
  });

  const res3 = await prisma.learningResource.create({
    data: {
      title: 'WRF Model Physics Schemes & Boundary Conditions Video Lecture',
      description: 'Recorded masterclass on microphysics parameterizations (WSM6 vs Thompson) and GFS initial condition downscaling.',
      format: ResourceFormat.VIDEO,
      fileUrl: '/uploads/sample-lecture.pdf',
      fileSize: 85000000,
      durationMinutes: 45,
      subject: 'Numerical Weather Prediction (NWP)',
      tags: ['WRF', 'Microphysics', 'Parameterization', 'HPC'],
      courseId: course3.id,
      trainerId: trainer3.id,
      downloadCount: 89
    }
  });

  // 5. Questionnaires with Calibrated Difficulty Questions (Adaptive & Standard)
  console.log('Creating Questionnaires with Easy, Medium, and Hard calibrated MCQs...');

  const quiz1 = await prisma.questionnaire.create({
    data: {
      title: 'Adaptive Assessment: Doppler Radar Analysis & Severe Weather Diagnostics',
      description: 'Dynamic IRT-lite calibrated assessment that tests your radar velocity interpretation, reflectivity calibration, and storm cell tracking.',
      courseId: course1.id,
      trainerId: trainer1.id,
      isAdaptive: true,
      durationMinutes: 25,
      passingScore: 70,
      status: 'ACTIVE',
      deadline: new Date(Date.now() + 14 * 24 * 3600 * 1000)
    }
  });

  // Easy Questions
  const q1 = await prisma.question.create({
    data: {
      questionnaireId: quiz1.id,
      text: 'In Doppler Weather Radar products, what does high reflectivity (Z > 55 dBZ) typically indicate in tropical thunderstorms?',
      explanation: 'Reflectivity values exceeding 50–55 dBZ typically indicate heavy rainfall with large raindrops and possible hail or high water content in convective cores.',
      difficulty: QuestionDifficulty.EASY,
      points: 1,
      orderIndex: 0,
      options: {
        create: [
          { text: 'Light continuous drizzle', isCorrect: false, orderIndex: 0 },
          { text: 'Heavy rainfall and severe convective core with large raindrops/hail', isCorrect: true, orderIndex: 1 },
          { text: 'Clear air turbulent echoes only', isCorrect: false, orderIndex: 2 },
          { text: 'Ground clutter without meteorological significance', isCorrect: false, orderIndex: 3 }
        ]
      }
    }
  });

  const q2 = await prisma.question.create({
    data: {
      questionnaireId: quiz1.id,
      text: 'Which radar wavelength is commonly used by IMD for S-band coastal Doppler Weather Radars to minimize rain attenuation during tropical cyclones?',
      explanation: 'S-band radars operate around 10 cm wavelength (2.7–3 GHz), which experiences minimal attenuation in heavy tropical rainfall.',
      difficulty: QuestionDifficulty.EASY,
      points: 1,
      orderIndex: 1,
      options: {
        create: [
          { text: '3 cm (X-band)', isCorrect: false, orderIndex: 0 },
          { text: '5 cm (C-band)', isCorrect: false, orderIndex: 1 },
          { text: '10 cm (S-band)', isCorrect: true, orderIndex: 2 },
          { text: '0.8 cm (Ka-band)', isCorrect: false, orderIndex: 3 }
        ]
      }
    }
  });

  // Medium Questions
  const q3 = await prisma.question.create({
    data: {
      questionnaireId: quiz1.id,
      text: 'What distinct signature on Doppler radial velocity displays signifies a Mesocyclone or rotating updraft in a supercell?',
      explanation: 'A Velocity Couplet (adjacent inbound and outbound maximum radial velocities closely spaced in azimuth) indicates rotation.',
      difficulty: QuestionDifficulty.MEDIUM,
      points: 2,
      orderIndex: 2,
      options: {
        create: [
          { text: 'Velocity Couplet with adjacent inbound and outbound radial velocities', isCorrect: true, orderIndex: 0 },
          { text: 'Uniform zero radial velocity ring across all azimuths', isCorrect: false, orderIndex: 1 },
          { text: 'High correlation coefficient (RhoHV > 0.99) with no shear', isCorrect: false, orderIndex: 2 },
          { text: 'Broad uniform negative velocity swath', isCorrect: false, orderIndex: 3 }
        ]
      }
    }
  });

  const q4 = await prisma.question.create({
    data: {
      questionnaireId: quiz1.id,
      text: 'In Dual-Polarization radar metrics, what does a Differential Reflectivity (ZDR) near 0 dB combined with high Z (55 dBZ) typically indicate?',
      explanation: 'Tumbling spherical hailstones produce near-zero differential reflectivity because they have no preferred horizontal orientation while maintaining high total reflectivity.',
      difficulty: QuestionDifficulty.MEDIUM,
      points: 2,
      orderIndex: 3,
      options: {
        create: [
          { text: 'Large oblate horizontally oriented raindrops', isCorrect: false, orderIndex: 0 },
          { text: 'Tumbling spherical hailstones with no preferred orientation', isCorrect: true, orderIndex: 1 },
          { text: 'Biological scatterers such as migrating birds', isCorrect: false, orderIndex: 2 },
          { text: 'Dry needle snow crystals aligned vertically', isCorrect: false, orderIndex: 3 }
        ]
      }
    }
  });

  // Hard Questions
  const q5 = await prisma.question.create({
    data: {
      questionnaireId: quiz1.id,
      text: 'Which dual-polarization parameter is immune to radar calibration errors and partial beam blockage, making it superior for quantitative precipitation estimation (QPE)?',
      explanation: 'Specific Differential Phase (KDP) is the range derivative of differential phase shift and is insensitive to absolute power calibration errors and partial beam blockage.',
      difficulty: QuestionDifficulty.HARD,
      points: 3,
      orderIndex: 4,
      options: {
        create: [
          { text: 'Equivalent Radar Reflectivity Factor (Z)', isCorrect: false, orderIndex: 0 },
          { text: 'Copolar Correlation Coefficient (RhoHV)', isCorrect: false, orderIndex: 1 },
          { text: 'Specific Differential Phase (KDP)', isCorrect: true, orderIndex: 2 },
          { text: 'Differential Reflectivity (ZDR)', isCorrect: false, orderIndex: 3 }
        ]
      }
    }
  });

  // 6. Enrollments, Attempts & Real Verified Certificates
  console.log('Generating Enrollments, Completed Courses, and Certificates...');

  // Trainee 1 completed Course 1 with Certificate
  const enrollment1 = await prisma.enrollment.create({
    data: {
      userId: trainee1.id,
      courseId: course1.id,
      status: EnrollmentStatus.COMPLETED,
      progressPercentage: 100,
      atRisk: false,
      completedAt: new Date(Date.now() - 2 * 24 * 3600 * 1000),
      lastAccessedAt: new Date(Date.now() - 1 * 24 * 3600 * 1000)
    }
  });

  // Attempt for Trainee 1
  await prisma.assessmentAttempt.create({
    data: {
      questionnaireId: quiz1.id,
      userId: trainee1.id,
      score: 8,
      maxScore: 9,
      percentage: 88.9,
      passed: true,
      isAdaptive: true,
      durationSeconds: 780,
      answers: [
        { questionId: q1.id, isCorrect: true, pointsEarned: 1 },
        { questionId: q2.id, isCorrect: true, pointsEarned: 1 },
        { questionId: q3.id, isCorrect: true, pointsEarned: 2 },
        { questionId: q4.id, isCorrect: true, pointsEarned: 2 },
        { questionId: q5.id, isCorrect: false, pointsEarned: 0 }
      ]
    }
  });

  // Generate Certificate with QR Code for Trainee 1
  const certNumber = 'IMD-CERT-2026-X8F9Q2';
  const verCode = 'IMD-7A9B3C';
  const qrUrl = `http://localhost:3000/verify/${verCode}`;
  const qrBase64 = await QRCode.toDataURL(qrUrl, { margin: 2, width: 250 });

  await prisma.certificate.create({
    data: {
      certificateNumber: certNumber,
      enrollmentId: enrollment1.id,
      userId: trainee1.id,
      courseId: course1.id,
      issueDate: new Date(),
      grade: 'Distinction',
      qrCodeDataUrl: qrBase64,
      pdfUrl: `/uploads/certificates/${certNumber}.pdf`,
      verificationCode: verCode
    }
  });

  // Trainee 2 enrolled in Course 2 (In Progress)
  await prisma.enrollment.create({
    data: {
      userId: trainee2.id,
      courseId: course2.id,
      status: EnrollmentStatus.IN_PROGRESS,
      progressPercentage: 65,
      atRisk: false,
      lastAccessedAt: new Date()
    }
  });

  // Trainee at Risk enrolled in Course 1 (Stalled, 0% progress, flagged at risk)
  await prisma.enrollment.create({
    data: {
      userId: traineeAtRisk.id,
      courseId: course1.id,
      status: EnrollmentStatus.ENROLLED,
      progressPercentage: 0,
      atRisk: true,
      riskReason: 'Inactive for 12 days; 0% course progress after enrollment',
      lastAccessedAt: new Date(Date.now() - 12 * 24 * 3600 * 1000),
      enrolledAt: new Date(Date.now() - 14 * 24 * 3600 * 1000)
    }
  });

  // 7. Feedback Submissions with Sentiment Analysis
  console.log('Submitting Course Feedbacks with Rule-Based Sentiment Tags...');

  const comment1 = 'Excellent lecture series! The explanation of velocity couplets and dual-pol KDP calculations was exceptionally clear and practical for our operational watch.';
  const s1 = SentimentAnalyzer.analyze(comment1, 5);

  await prisma.feedback.create({
    data: {
      courseId: course1.id,
      userId: trainee1.id,
      trainerId: trainer1.id,
      rating: 5,
      contentRating: 5,
      deliveryRating: 5,
      comments: comment1,
      sentimentTag: s1.tag as any,
      sentimentScore: s1.score
    }
  });

  const comment2 = 'Very good overview of INSAT-3D channels, but could use more case studies on rapid cyclone intensification in the Arabian Sea.';
  const s2 = SentimentAnalyzer.analyze(comment2, 4);

  await prisma.feedback.create({
    data: {
      courseId: course2.id,
      userId: trainee2.id,
      trainerId: trainer2.id,
      rating: 4,
      contentRating: 4,
      deliveryRating: 4,
      comments: comment2,
      sentimentTag: s2.tag as any,
      sentimentScore: s2.score
    }
  });

  // 8. System Announcements & Notifications
  console.log('Broadcasting Announcements...');

  await prisma.notification.createMany({
    data: [
      {
        title: 'Monsoon 2026 Capacity Building Mission Launched',
        message: 'All IMD and MoES operational scientific staff are required to complete the Radar Meteorology and NWP model interpretation modules prior to June 1st.',
        type: NotificationType.ANNOUNCEMENT,
        targetRole: null, // Broadcast to all
        isRead: false
      },
      {
        title: 'New Adaptive MCQ Engine Activated',
        message: 'Assessments now dynamically adjust question difficulty based on your running accuracy. Test your knowledge on IMD-RAD-201!',
        type: NotificationType.SYSTEM,
        targetRole: Role.TRAINEE,
        isRead: false
      },
      {
        title: 'Certificate Awarded: Radar Meteorology',
        message: 'Congratulations Rohan Verma! You have successfully earned your verified Certificate of Excellence in Doppler Weather Radar Interpretation.',
        type: NotificationType.CERTIFICATE,
        recipientId: trainee1.id,
        link: `/certificates`,
        isRead: false
      }
    ]
  });

  // 9. Trainee Achievements
  await prisma.achievement.createMany({
    data: [
      {
        userId: trainee1.id,
        badgeType: 'FIRST_COMPLETION',
        title: 'Pioneer Graduate',
        description: 'Successfully completed first official IMD capacity course with Distinction.'
      },
      {
        userId: trainee1.id,
        badgeType: 'RADAR_SPECIALIST',
        title: 'Radar Diagnostic Expert',
        description: 'Achieved >85% score on Dual-Polarimetric Doppler Radar Assessment.'
      }
    ]
  });

  console.log('✅ CAPACITY CONNECT Database Seed Completed Successfully!');
  console.log('---------------------------------------------------------');
  console.log('Demo Accounts for Verification:');
  console.log('👑 Admin:   admin@imd.gov.in           / Admin@123');
  console.log('👨‍🏫 Trainer: trainer.radar@imd.gov.in   / Password@123');
  console.log('🧑‍🎓 Trainee: trainee1@imd.gov.in        / Password@123');
  console.log('⚠️ At-Risk: trainee.atrisk@imd.gov.in  / Password@123');
  console.log('⏳ Pending: trainer.pending@imd.gov.in / Password@123');
  console.log('---------------------------------------------------------');
}

main()
  .catch((e) => {
    console.error('❌ Seed error:', e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
